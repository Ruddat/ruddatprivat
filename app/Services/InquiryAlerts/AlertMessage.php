<?php

namespace App\Services\InquiryAlerts;

use App\Models\Inquiry;
use Illuminate\Support\Str;

/**
 * Inhalt einer internen Benachrichtigung.
 *
 * Bewusst minimal: Unternehmen (bereinigt und gekürzt), Thema und Vorgangsnummer.
 * Name, E-Mail, Telefonnummer und Beschreibung werden NICHT an externe
 * Nachrichtendienste übermittelt – die Details stehen im Adminbereich.
 */
final class AlertMessage
{
    public function __construct(
        public readonly string $company,
        public readonly string $topic,
        public readonly ?string $reference,
        public readonly ?string $adminUrl,
        public readonly bool $isTest = false,
        public readonly ?string $contactName = null,
    ) {
    }

    public static function fromInquiry(Inquiry $inquiry): self
    {
        return new self(
            company: self::sanitize($inquiry->company),
            topic: $inquiry->type->label(),
            reference: $inquiry->reference,
            adminUrl: route('admin.inquiries.show', $inquiry),
            contactName: self::sanitize($inquiry->name, 60),
        );
    }

    public static function test(): self
    {
        return new self(
            company: 'Testnachricht',
            topic: 'Kanalprüfung',
            reference: null,
            adminUrl: route('admin.inquiry-alerts.settings'),
            isTest: true,
        );
    }

    /**
     * Kurztext für SMS / Telegram, z. B.
     * „RuddatTech: Neue Anfrage von Musterfirma GmbH. Thema: Managed Application Services. Details im Adminbereich.“
     */
    public function shortText(): string
    {
        if ($this->isTest) {
            return 'RuddatTech: Testbenachrichtigung. Der Kanal ist korrekt eingerichtet.';
        }

        return "RuddatTech: Neue Anfrage von {$this->company}. Thema: {$this->topic}. Details im Adminbereich.";
    }

    /**
     * Entfernt Steuerzeichen, Links und Sonderzeichen aus Nutzereingaben und kürzt sie,
     * damit Formularinhalte nicht ungeprüft an Nachrichtendienste gelangen.
     */
    public static function sanitize(string $value, int $limit = 40): string
    {
        $clean = Str::of($value)
            ->replaceMatches('~https?://\S+|www\.\S+~i', '')
            ->replaceMatches('/[^\p{L}\p{N} &.,\-()+\/]/u', '')
            ->squish()
            ->limit($limit, '…')
            ->value();

        return $clean !== '' ? $clean : 'unbekannt';
    }
}
