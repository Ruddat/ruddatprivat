<?php

namespace App\Services\InquiryAlerts;

use App\Models\Setting;

/**
 * Nicht geheime Einstellungen der Benachrichtigungen (Schalter, Empfänger).
 * Werte aus dem Adminbereich (Tabelle settings) überschreiben die Konfiguration.
 * Zugangsdaten bleiben ausschließlich in der .env.
 */
class AlertSettings
{
    public const CHANNELS = ['mail', 'sms', 'telegram', 'whatsapp'];

    /** Kanäle, deren Empfänger im Admin geändert werden dürfen */
    public const EDITABLE_RECIPIENTS = ['mail', 'sms', 'whatsapp'];

    public function enabled(string $channel): bool
    {
        $stored = Setting::get($this->key($channel, 'enabled'));

        return (bool) ($stored ?? config("inquiry-alerts.channels.$channel.enabled", false));
    }

    public function recipient(string $channel): ?string
    {
        $configKey = $channel === 'telegram' ? 'chat_id' : 'to';
        $default = config("inquiry-alerts.channels.$channel.$configKey");

        if (! in_array($channel, self::EDITABLE_RECIPIENTS, true)) {
            return $default ?: null;
        }

        $stored = Setting::get($this->key($channel, 'recipient'));

        return ($stored ?: $default) ?: null;
    }

    public function setEnabled(string $channel, bool $enabled): void
    {
        $this->store($channel, 'enabled', $enabled, 'boolean');
    }

    public function setRecipient(string $channel, ?string $recipient): void
    {
        if (in_array($channel, self::EDITABLE_RECIPIENTS, true)) {
            $this->store($channel, 'recipient', $recipient ?: null, 'string');
        }
    }

    private function store(string $channel, string $field, mixed $value, string $type): void
    {
        Setting::updateOrCreate(
            ['key' => $this->key($channel, $field)],
            ['value' => $value, 'type' => $type, 'group' => 'inquiry_alerts', 'description' => "Benachrichtigung $channel: $field"],
        );
    }

    private function key(string $channel, string $field): string
    {
        return "inquiry_alerts.$channel.$field";
    }
}
