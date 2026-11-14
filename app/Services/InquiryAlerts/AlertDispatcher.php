<?php

namespace App\Services\InquiryAlerts;

use App\Jobs\SendInquiryAlert;
use App\Models\Inquiry;
use App\Models\InquiryAlert;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Str;

/**
 * Legt für eine Anfrage je aktivem Kanal genau einen Protokolleintrag an
 * und stößt den Versand als Queue-Job an.
 */
class AlertDispatcher
{
    public function __construct(private ChannelRegistry $channels, private AlertSettings $settings)
    {
    }

    public function dispatchForInquiry(Inquiry $inquiry): void
    {
        foreach (AlertSettings::CHANNELS as $key) {
            if (! $this->settings->enabled($key)) {
                continue;
            }

            $this->queue($key, "inquiry:{$inquiry->id}:{$key}", $inquiry);
        }
    }

    /**
     * Ersatzkanal nach endgültigem Fehlschlag. Wird pro Anfrage höchstens einmal je Kanal angelegt.
     */
    public function dispatchFallback(InquiryAlert $failed): void
    {
        $fallback = config("inquiry-alerts.fallback.{$failed->channel}");

        if (! $fallback || $failed->is_fallback || ! $this->settings->enabled($fallback)) {
            return;
        }

        $scope = $failed->inquiry_id ? "inquiry:{$failed->inquiry_id}" : "test:{$failed->id}";
        $this->queue($fallback, "{$scope}:fallback:{$fallback}", $failed->inquiry, isFallback: true, isTest: $failed->is_test);
    }

    public function dispatchTest(string $key): InquiryAlert
    {
        return $this->queue($key, 'test:' . Str::uuid(), null, isTest: true, force: true);
    }

    private function queue(string $key, string $dedupeKey, ?Inquiry $inquiry, bool $isFallback = false, bool $isTest = false, bool $force = false): ?InquiryAlert
    {
        $channel = $this->channels->get($key);
        $configured = $channel->isConfigured();

        try {
            $alert = InquiryAlert::create([
                'inquiry_id' => $inquiry?->id,
                'channel' => $key,
                'provider' => $channel->provider(),
                'recipient_masked' => InquiryAlert::mask($channel->recipient()),
                'status' => $configured ? InquiryAlert::QUEUED : InquiryAlert::SKIPPED,
                'last_error' => $configured ? null : 'Kanal ist aktiviert, aber nicht vollständig konfiguriert.',
                'is_test' => $isTest,
                'is_fallback' => $isFallback,
                'dedupe_key' => $dedupeKey,
            ]);
        } catch (UniqueConstraintViolationException) {
            return null; // Alarm für diese Anfrage und diesen Kanal existiert bereits
        }

        if ($configured) {
            SendInquiryAlert::dispatch($alert->id)->afterCommit();
        }

        return $alert;
    }
}
