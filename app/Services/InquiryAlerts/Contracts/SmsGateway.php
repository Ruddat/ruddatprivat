<?php

namespace App\Services\InquiryAlerts\Contracts;

use App\Services\InquiryAlerts\AlertDeliveryException;

/**
 * Austauschbarer SMS-Anbieter.
 */
interface SmsGateway
{
    public function name(): string;

    public function isConfigured(): bool;

    /**
     * @return string|null Nachrichten-ID des Anbieters
     *
     * @throws AlertDeliveryException
     */
    public function send(string $to, string $text, string $from): ?string;
}
