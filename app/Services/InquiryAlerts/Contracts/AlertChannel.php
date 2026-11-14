<?php

namespace App\Services\InquiryAlerts\Contracts;

use App\Services\InquiryAlerts\AlertDeliveryException;
use App\Services\InquiryAlerts\AlertMessage;

interface AlertChannel
{
    /** Interner Schlüssel: mail, sms, telegram, whatsapp */
    public function key(): string;

    /** Name des Anbieters für das Protokoll, z. B. "seven.io" */
    public function provider(): string;

    /** Sind alle nötigen Zugangsdaten und Empfänger vorhanden? */
    public function isConfigured(): bool;

    /** Empfänger (für maskierte Anzeige im Protokoll) */
    public function recipient(): ?string;

    /**
     * Übergibt die Nachricht an den Anbieter.
     *
     * @return string|null Nachrichten-ID des Anbieters, falls geliefert
     *
     * @throws AlertDeliveryException wenn der Anbieter die Nachricht nicht annimmt
     */
    public function send(AlertMessage $message): ?string;
}
