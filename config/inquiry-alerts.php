<?php

/*
|--------------------------------------------------------------------------
| Sofortbenachrichtigung bei neuen Anfragen
|--------------------------------------------------------------------------
|
| Zugangsdaten und Tokens kommen ausschließlich aus der .env und werden nie
| im Repository oder in der Datenbank gespeichert. Schalter und Empfänger
| können im Adminbereich überschrieben werden (Tabelle settings).
|
| Externe Kanäle (SMS, Telegram, WhatsApp) sind standardmäßig AUS und lassen
| sich nur aktivieren, wenn die jeweilige Konfiguration vollständig ist.
|
*/

return [

    'channels' => [

        'mail' => [
            'enabled' => env('INQUIRY_ALERT_MAIL_ENABLED', true),
            'to' => env('INQUIRY_ALERT_MAIL_TO', env('MAIL_FROM_ADDRESS')),
        ],

        'sms' => [
            'enabled' => env('INQUIRY_ALERT_SMS_ENABLED', false),
            'to' => env('INQUIRY_SMS_TO'),
            // "seven" = echter Versand über seven.io, "log" = nur ins Log schreiben (Entwicklung)
            'driver' => env('INQUIRY_SMS_DRIVER', 'log'),
            'from' => env('INQUIRY_SMS_FROM', 'RuddatTech'),
        ],

        'telegram' => [
            'enabled' => env('INQUIRY_ALERT_TELEGRAM_ENABLED', false),
            'chat_id' => env('TELEGRAM_CHAT_ID'),
        ],

        'whatsapp' => [
            // Nur mit eingerichteter WhatsApp Business Platform (Cloud API), freigegebenem
            // Template und Zustimmung des Empfängers.
            'enabled' => env('INQUIRY_ALERT_WHATSAPP_ENABLED', false),
            'to' => env('WHATSAPP_ALERT_TO'),
            'phone_number_id' => env('WHATSAPP_PHONE_NUMBER_ID'),
            'template' => env('WHATSAPP_ALERT_TEMPLATE'),
            'language' => env('WHATSAPP_ALERT_TEMPLATE_LANGUAGE', 'de'),
            'api_version' => env('WHATSAPP_API_VERSION', 'v21.0'),
        ],
    ],

    // Fallback, wenn ein Kanal endgültig scheitert: Kanal => Ersatzkanal
    'fallback' => [
        'sms' => 'mail',
        'telegram' => 'mail',
        'whatsapp' => 'mail',
        'mail' => 'sms',
    ],

    // Wiederholungen bei Fehlern (Sekunden zwischen den Versuchen)
    'retry_backoff' => [30, 120, 600, 1800],

    // Öffentlicher WhatsApp-Kontaktbutton (wa.me-Link, ohne serverseitige Erfassung)
    'whatsapp_button_number' => env('WHATSAPP_CONTACT_NUMBER'),

];
