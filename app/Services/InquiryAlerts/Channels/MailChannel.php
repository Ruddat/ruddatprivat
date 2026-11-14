<?php

namespace App\Services\InquiryAlerts\Channels;

use App\Mail\InquiryAlertMail;
use App\Services\InquiryAlerts\AlertDeliveryException;
use App\Services\InquiryAlerts\AlertMessage;
use App\Services\InquiryAlerts\AlertSettings;
use App\Services\InquiryAlerts\Contracts\AlertChannel;
use Illuminate\Support\Facades\Mail;
use Throwable;

class MailChannel implements AlertChannel
{
    public function __construct(private AlertSettings $settings)
    {
    }

    public function key(): string
    {
        return 'mail';
    }

    public function provider(): string
    {
        return 'mail:' . config('mail.default');
    }

    public function isConfigured(): bool
    {
        return filter_var($this->recipient(), FILTER_VALIDATE_EMAIL) !== false;
    }

    public function recipient(): ?string
    {
        return $this->settings->recipient('mail');
    }

    public function send(AlertMessage $message): ?string
    {
        try {
            $sent = Mail::to($this->recipient())->send(new InquiryAlertMail($message));
        } catch (Throwable $e) {
            throw new AlertDeliveryException('E-Mail-Versand fehlgeschlagen: ' . $e->getMessage(), previous: $e);
        }

        return $sent?->getMessageId();
    }
}
