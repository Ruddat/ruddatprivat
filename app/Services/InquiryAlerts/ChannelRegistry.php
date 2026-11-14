<?php

namespace App\Services\InquiryAlerts;

use App\Services\InquiryAlerts\Channels\MailChannel;
use App\Services\InquiryAlerts\Channels\SmsChannel;
use App\Services\InquiryAlerts\Channels\TelegramChannel;
use App\Services\InquiryAlerts\Channels\WhatsAppChannel;
use App\Services\InquiryAlerts\Contracts\AlertChannel;
use InvalidArgumentException;

class ChannelRegistry
{
    private const MAP = [
        'mail' => MailChannel::class,
        'sms' => SmsChannel::class,
        'telegram' => TelegramChannel::class,
        'whatsapp' => WhatsAppChannel::class,
    ];

    public function get(string $key): AlertChannel
    {
        $class = self::MAP[$key] ?? throw new InvalidArgumentException("Unbekannter Kanal: $key");

        return app($class);
    }

    /** @return array<string, AlertChannel> */
    public function all(): array
    {
        return collect(array_keys(self::MAP))->mapWithKeys(fn ($k) => [$k => $this->get($k)])->all();
    }
}
