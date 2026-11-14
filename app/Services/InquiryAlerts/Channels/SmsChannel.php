<?php

namespace App\Services\InquiryAlerts\Channels;

use App\Services\InquiryAlerts\AlertMessage;
use App\Services\InquiryAlerts\AlertSettings;
use App\Services\InquiryAlerts\Contracts\AlertChannel;
use App\Services\InquiryAlerts\Contracts\SmsGateway;

class SmsChannel implements AlertChannel
{
    public function __construct(private AlertSettings $settings, private SmsGateway $gateway)
    {
    }

    public function key(): string
    {
        return 'sms';
    }

    public function provider(): string
    {
        return $this->gateway->name();
    }

    public function isConfigured(): bool
    {
        return $this->gateway->isConfigured() && self::isValidNumber($this->recipient());
    }

    public function recipient(): ?string
    {
        return $this->settings->recipient('sms');
    }

    public function send(AlertMessage $message): ?string
    {
        return $this->gateway->send(
            $this->recipient(),
            $message->shortText(),
            (string) config('inquiry-alerts.channels.sms.from', 'RuddatTech'),
        );
    }

    /** Internationale Rufnummer, z. B. +4917612345678 */
    public static function isValidNumber(?string $number): bool
    {
        return is_string($number) && preg_match('/^\+[1-9]\d{6,14}$/', $number) === 1;
    }
}
