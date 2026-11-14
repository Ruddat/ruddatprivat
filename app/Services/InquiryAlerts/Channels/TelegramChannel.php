<?php

namespace App\Services\InquiryAlerts\Channels;

use App\Services\InquiryAlerts\AlertDeliveryException;
use App\Services\InquiryAlerts\AlertMessage;
use App\Services\InquiryAlerts\AlertSettings;
use App\Services\InquiryAlerts\Contracts\AlertChannel;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

/**
 * Kurzinfo an einen Telegram-Chat über die Bot-API.
 * Nutzt TELEGRAM_BOT_TOKEN / TELEGRAM_CHAT_ID aus der .env.
 */
class TelegramChannel implements AlertChannel
{
    public function __construct(private AlertSettings $settings)
    {
    }

    public function key(): string
    {
        return 'telegram';
    }

    public function provider(): string
    {
        return 'telegram';
    }

    public function isConfigured(): bool
    {
        return filled(config('services.telegram.bot_token')) && filled($this->recipient());
    }

    public function recipient(): ?string
    {
        return $this->settings->recipient('telegram');
    }

    public function send(AlertMessage $message): ?string
    {
        $token = (string) config('services.telegram.bot_token');

        try {
            $response = Http::timeout(15)->asJson()->post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => $this->recipient(),
                'text' => $message->shortText(),
                'disable_web_page_preview' => true,
            ]);
        } catch (ConnectionException $e) {
            // Token kann in der Fehlermeldung (URL) stehen – nicht weitergeben
            throw new AlertDeliveryException('Telegram nicht erreichbar.', previous: $e);
        }

        if (! $response->successful() || $response->json('ok') !== true) {
            throw new AlertDeliveryException('Telegram hat die Nachricht nicht angenommen (HTTP ' . $response->status() . ').');
        }

        return (string) $response->json('result.message_id');
    }
}
