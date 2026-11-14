<?php

namespace App\Jobs;

use App\Models\InquiryAlert;
use App\Services\InquiryAlerts\AlertDispatcher;
use App\Services\InquiryAlerts\AlertMessage;
use App\Services\InquiryAlerts\ChannelRegistry;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use Throwable;

/**
 * Versendet genau eine Benachrichtigung (ein Kanal, eine Anfrage).
 *
 * - Wiederholt fehlgeschlagene Zustellungen automatisch mit wachsendem Abstand.
 * - Ist idempotent: bereits angenommene Alarme werden nicht erneut gesendet.
 * - Stößt nach dem letzten Fehlversuch den Fallback-Kanal an.
 */
class SendInquiryAlert implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;

    public int $uniqueFor = 3600;

    public function __construct(public int $alertId)
    {
    }

    public function uniqueId(): string
    {
        return (string) $this->alertId;
    }

    public function backoff(): array
    {
        return config('inquiry-alerts.retry_backoff', [30, 120, 600, 1800]);
    }

    public function handle(ChannelRegistry $channels): void
    {
        $alert = InquiryAlert::with('inquiry')->find($this->alertId);

        if (! $alert || $alert->status !== InquiryAlert::QUEUED) {
            return; // gelöscht, bereits angenommen oder übersprungen
        }

        $alert->increment('attempts');

        $message = $alert->is_test || ! $alert->inquiry
            ? AlertMessage::test()
            : AlertMessage::fromInquiry($alert->inquiry);

        try {
            $providerId = $channels->get($alert->channel)->send($message);
        } catch (Throwable $e) {
            $alert->update(['last_error' => self::safeError($e)]);

            throw $e; // Queue wiederholt den Job gemäß backoff()
        }

        $alert->update([
            'status' => InquiryAlert::ACCEPTED,
            'provider_message_id' => $providerId ? Str::limit($providerId, 120, '') : null,
            'accepted_at' => now(),
            'last_error' => null,
        ]);
    }

    /** Wird nach dem letzten fehlgeschlagenen Versuch aufgerufen. */
    public function failed(?Throwable $e): void
    {
        $alert = InquiryAlert::find($this->alertId);

        if (! $alert || $alert->status === InquiryAlert::ACCEPTED) {
            return;
        }

        $alert->update([
            'status' => InquiryAlert::FAILED,
            'failed_at' => now(),
            'last_error' => $e ? self::safeError($e) : $alert->last_error,
        ]);

        app(AlertDispatcher::class)->dispatchFallback($alert);
    }

    private static function safeError(Throwable $e): string
    {
        // Tokens könnten in URLs von Fehlermeldungen stehen
        $msg = preg_replace('~bot\d+:[\w-]+~', 'bot***', $e->getMessage());

        return Str::limit((string) $msg, 500);
    }
}
