<?php

/*
|--------------------------------------------------------------------------
| Pflichtangaben für Impressum und Datenschutzerklärung
|--------------------------------------------------------------------------
|
| Diese Angaben werden in der .env gepflegt. Ohne ladungsfähige Anschrift ist
| das Impressum nicht vollständig (§ 5 DDG) – vor dem Livegang setzen.
|
*/

return [
    'legal' => [
        'owner' => env('LEGAL_OWNER', 'Ingo Ruddat'),
        'business_name' => env('LEGAL_BUSINESS_NAME', 'RuddatTech'),
        'street' => env('LEGAL_STREET'),
        'zip_city' => env('LEGAL_ZIP_CITY'),
        'country' => env('LEGAL_COUNTRY', 'Deutschland'),
        'email' => env('LEGAL_EMAIL', 'service@ruddattech.de'),
        'phone' => env('LEGAL_PHONE'),
        'vat_id' => env('LEGAL_VAT_ID'),
        // Hosting-Anbieter für die Datenschutzerklärung, z. B. "Hetzner Online GmbH, Gunzenhausen"
        'hoster' => env('LEGAL_HOSTER'),
        // Zuständige Aufsichtsbehörde
        'authority' => env('LEGAL_AUTHORITY', 'Die Landesbeauftragte für den Datenschutz Niedersachsen, Prinzenstraße 5, 30159 Hannover'),
        'privacy_updated' => env('LEGAL_PRIVACY_UPDATED', 'November 2026'),
    ],
];
