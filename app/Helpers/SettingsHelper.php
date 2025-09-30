<?php

namespace App\Helpers;

use App\Models\Setting;

class SettingsHelper
{
    /**
     * Standardwerte für Settings
     * => group, key, value, type, description
     */
    protected static array $defaults = [
        [
            'group'       => 'company',
            'key'         => 'name',
            'value'       => 'Ruddattech',
            'type'        => 'string',
            'description' => 'Firmenname',
        ],
        [
            'group'       => 'company',
            'key'         => 'address',
            'value'       => 'Musterstraße 1',
            'type'        => 'string',
            'description' => 'Firmenadresse',
        ],
        [
            'group'       => 'company',
            'key'         => 'city',
            'value'       => '12345 Musterstadt',
            'type'        => 'string',
            'description' => 'Ort, PLZ',
        ],
        [
            'group'       => 'company',
            'key'         => 'state',
            'value'       => 'Niedersachsen',
            'type'        => 'string',
            'description' => 'Bundesland',
        ],
        [
            'group'       => 'company',
            'key'         => 'country',
            'value'       => 'Deutschland',
            'type'        => 'string',
            'description' => 'Land',
        ],

        [ 
            'group'       => 'company',
            'key'         => 'tax_id',
            'value'       => 'DE123456789',
            'type'        => 'string',
            'description' => 'Steuernummer / USt-ID',
        ],
        [
            'group'       => 'company',
            'key'         => 'website',
            'value'       => 'https://www.ruddattech.de',
            'type'        => 'string',
            'description' => 'Firmenwebsite',
        ],
        [
            'group'       => 'company',
            'key'         => 'phone',
            'value'       => '+49 123 456789',
            'type'        => 'string',
            'description' => 'Telefonnummer',
        ],
        [
            'group'       => 'company',
            'key'         => 'email',
            'value'       => 'service@ruddattech.de',
            'type'        => 'string',
            'description' => 'Support E-Mail',
        ],

        // Limits
        [
            'group'       => 'limits',
            'key'         => 'invoices',
            'value'       => '50',
            'type'        => 'number',
            'description' => 'Maximale Anzahl Rechnungen',
        ],
        [
            'group'       => 'limits',
            'key'         => 'tenants',
            'value'       => '10',
            'type'        => 'number',
            'description' => 'Maximale Anzahl Mieter',
        ],
        [
            'group'       => 'limits',
            'key'         => 'storage',
            'value'       => '5120',
            'type'        => 'number',
            'description' => 'Speicherplatz in MB',
        ],

// Social
[
    'group'       => 'social',
    'key'         => 'twitter',
    'value'       => '',
    'type'        => 'string',
    'description' => 'Twitter Profil-URL',
],
[
    'group'       => 'social',
    'key'         => 'facebook',
    'value'       => '',
    'type'        => 'string',
    'description' => 'Facebook Profil-URL',
],
[
    'group'       => 'social',
    'key'         => 'instagram',
    'value'       => '',
    'type'        => 'string',
    'description' => 'Instagram Profil-URL',
],
[
    'group'       => 'social',
    'key'         => 'linkedin',
    'value'       => '',
    'type'        => 'string',
    'description' => 'LinkedIn Profil-URL',
],

// Contact
[
    'group'       => 'contact',
    'key'         => 'phone',
    'value'       => '+49 123 456789',
    'type'        => 'string',
    'description' => 'Kontakt Telefonnummer',
],
[
    'group'       => 'contact',
    'key'         => 'email',
    'value'       => 'service@ruddattech.de',
    'type'        => 'string',
    'description' => 'Kontakt E-Mail',
],
[
    'group'       => 'contact',
    'key'         => 'address',
    'value'       => 'Braunschweig, NI, Deutschland',
    'type'        => 'string',
    'description' => 'Adresse',
],
[
    'group'       => 'contact',
    'key'         => 'opening_hours',
    'value'       => "Montag - Freitag: 09:00 – 22:00 Uhr\nSamstag: 09:00 – 18:00 Uhr\nSonntag: 09:00 – 12:00 Uhr",
    'type'        => 'text',
    'description' => 'Öffnungszeiten',
],

// Pricing Plans
[
    'group'       => 'pricing',
    'key'         => 'basic',
    'value'       => '0',
    'type'        => 'number',
    'description' => 'Kostenloser Plan (monatlich, EUR)',
],
[
    'group'       => 'pricing',
    'key'         => 'pro',
    'value'       => '19.90',
    'type'        => 'number',
    'description' => 'Pro Plan (monatlich, EUR)',
],
[
    'group'       => 'pricing',
    'key'         => 'enterprise',
    'value'       => '99.00',
    'type'        => 'number',
    'description' => 'Enterprise Plan (monatlich, EUR)',
],
[
    'group'       => 'notifications',
    'key'         => 'feedback_mail',
    'value'       => '1',
    'type'        => 'boolean',
    'description' => 'Mail-Benachrichtigung bei neuem Feedback',
],
[
    'group'       => 'notifications',
    'key'         => 'feedback_inapp',
    'value'       => '1',
    'type'        => 'boolean',
    'description' => 'In-App Notification im Header bei neuem Feedback',
],


    ];

    /**
     * Standardwerte in DB booten (nur wenn nicht vorhanden)
     */
    public static function bootDefaults(): void
    {
        foreach (self::$defaults as $setting) {
            Setting::firstOrCreate(
                ['group' => $setting['group'], 'key' => $setting['key']],
                [
                    'value'       => $setting['value'],
                    'type'        => $setting['type'],
                    'description' => $setting['description'],
                ]
            );
        }
    }

    /**
     * Setting holen
     */
    public static function get(string $groupKey, $default = null)
    {
        [$group, $key] = explode('.', $groupKey);

        return Setting::where('group', $group)
            ->where('key', $key)
            ->value('value') ?? $default;
    }

    /**
     * Setting setzen
     */
    public static function set(string $groupKey, $value): void
    {
        [$group, $key] = explode('.', $groupKey);

        Setting::updateOrCreate(
            ['group' => $group, 'key' => $key],
            ['value' => $value]
        );
    }
}
