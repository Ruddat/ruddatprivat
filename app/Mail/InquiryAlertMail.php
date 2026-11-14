<?php

namespace App\Mail;

use App\Services\InquiryAlerts\AlertMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/**
 * Interne Benachrichtigung an die Geschäftsadresse (nicht an den Anfragenden).
 * Wird vom Queue-Job synchron versendet, daher kein ShouldQueue.
 */
class InquiryAlertMail extends Mailable
{
    use Queueable;

    public function __construct(public AlertMessage $alert)
    {
    }

    public function envelope(): Envelope
    {
        $subject = $this->alert->isTest
            ? 'RuddatTech: Testbenachrichtigung'
            : "Neue Anfrage {$this->alert->reference} – {$this->alert->topic}";

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(text: 'mail.inquiry-alert');
    }
}
