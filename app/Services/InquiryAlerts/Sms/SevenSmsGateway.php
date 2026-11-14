<?php

namespace App\Services\InquiryAlerts\Sms;

use App\Services\InquiryAlerts\AlertDeliveryException;
use App\Services\InquiryAlerts\Contracts\SmsGateway;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

/**
 * SMS-Versand über seven.io (https://docs.seven.io/de/rest-api/endpunkte/sms).
 *
 * Ein Erfolgscode "100" bedeutet: Der Anbieter hat die Nachricht angenommen.
 * Das ist keine Bestätigung der Zustellung oder des Lesens auf dem Telefon.
 */
class SevenSmsGateway implements SmsGateway
{
    public function name(): string
    {
        return 'seven.io';
    }

    public function isConfigured(): bool
    {
        return filled(config('services.seven.api_key'));
    }

    public function send(string $to, string $text, string $from): ?string
    {
        try {
            $response = Http::timeout(15)
                ->acceptJson()
                ->withHeaders(['X-Api-Key' => (string) config('services.seven.api_key')])
                ->asForm()
                ->post(config('services.seven.endpoint'), [
                    'to' => $to,
                    'text' => $text,
                    'from' => $from,
                ]);
        } catch (ConnectionException $e) {
            throw new AlertDeliveryException('seven.io nicht erreichbar: ' . $e->getMessage(), previous: $e);
        }

        if (! $response->successful()) {
            throw new AlertDeliveryException('seven.io HTTP ' . $response->status());
        }

        $code = (string) ($response->json('success') ?? $response->body());

        if ($code !== '100') {
            // Keine Antwortdetails protokollieren, die Kontodaten enthalten könnten
            throw new AlertDeliveryException("seven.io hat die SMS nicht angenommen (Code {$code}).");
        }

        return $response->json('messages.0.id') !== null ? (string) $response->json('messages.0.id') : null;
    }
}
