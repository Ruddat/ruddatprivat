<?php

namespace App\Listeners;

use App\Events\InquiryReceived;
use App\Services\InquiryAlerts\AlertDispatcher;

/**
 * Stößt nach dem Speichern einer Anfrage die Benachrichtigungen an.
 * Läuft synchron (nur Protokolleinträge + Job-Dispatch); der eigentliche
 * Versand erfolgt im Queue-Worker.
 */
class QueueInquiryAlerts
{
    public function __construct(private AlertDispatcher $dispatcher)
    {
    }

    public function handle(InquiryReceived $event): void
    {
        $this->dispatcher->dispatchForInquiry($event->inquiry);
    }
}
