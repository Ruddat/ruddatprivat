<?php

namespace App\Services\InquiryAlerts\Channels;

use App\Services\InquiryAlerts\AlertDeliveryException;
use App\Services\InquiryAlerts\AlertMessage;
use App\Services\InquiryAlerts\AlertSettings;
use App\Services\InquiryAlerts\Contracts\AlertChannel;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

/**
 * WhatsApp Business Platform (Cloud API).
 *
 * Voraussetzungen: eingerichtetes Business-Konto, Telefonnummern-ID, Zugriffstoken,
 * ein von Meta freigegebenes Template mit zwei Platzhaltern ({{1}} Unternehmen,
 * {{2}} Thema) und die Zustimmung des Empfängers. Ohne vollständige Konfiguration
 * bleibt der Kanal inaktiv.
 *
 * Nicht zu verwechseln mit dem öffentlichen WhatsApp-Button der Webseite:
 * Der Button öffnet nur einen normalen Chat, Nachrichten darüber werden NICHT
 * serverseitig erfasst.
 */
class WhatsAppChannel implements AlertChannel
{
    public function __construct(private AlertSettings $settings)
    {
    }

    public function key(): string
    {
        return 'whatsapp';
    }

    public function provider(): string
    {
        return 'whatsapp-cloud-api';
    }

    public function isConfigured(): bool
    {
        return filled(config('services.whatsapp.token'))
            && filled(config('inquiry-alerts.channels.whatsapp.phone_number_id'))
            && filled(config('inquiry-alerts.channels.whatsapp.template'))
            && SmsChannel::isValidNumber($this->recipient());
    }

    public function recipient(): ?string
    {
        return $this->settings->recipient('whatsapp');
    }

    public function send(AlertMessage $message): ?string
    {
        $cfg = config('inquiry-alerts.channels.whatsapp');
        $url = sprintf('https://graph.facebook.com/%s/%s/messages', $cfg['api_version'], $cfg['phone_number_id']);

        try {
            $response = Http::timeout(15)
                ->withToken((string) config('services.whatsapp.token'))
                ->asJson()
                ->post($url, [
                    'messaging_product' => 'whatsapp',
                    'to' => ltrim((string) $this->recipient(), '+'),
                    'type' => 'template',
                    'template' => [
                        'name' => $cfg['template'],
                        'language' => ['code' => $cfg['language']],
                        'components' => [[
                            'type' => 'body',
                            'parameters' => [
                                ['type' => 'text', 'text' => $message->company],
                                ['type' => 'text', 'text' => $message->topic],
                            ],
                        ]],
                    ],
                ]);
        } catch (ConnectionException $e) {
            throw new AlertDeliveryException('WhatsApp Cloud API nicht erreichbar.', previous: $e);
        }

        if (! $response->successful()) {
            $error = $response->json('error.message') ?? 'unbekannter Fehler';
            throw new AlertDeliveryException('WhatsApp hat die Nachricht nicht angenommen (HTTP ' . $response->status() . '): ' . mb_substr((string) $error, 0, 200));
        }

        return $response->json('messages.0.id');
    }
}
