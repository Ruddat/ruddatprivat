<?php

namespace App\Services\InquiryAlerts\Sms;

use App\Services\InquiryAlerts\Contracts\SmsGateway;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Entwicklungstreiber: schreibt die SMS nur ins Log, versendet nichts.
 */
class LogSmsGateway implements SmsGateway
{
    public function name(): string
    {
        return 'log';
    }

    public function isConfigured(): bool
    {
        return true;
    }

    public function send(string $to, string $text, string $from): ?string
    {
        Log::info('[SMS-Log] Kein echter Versand', ['to' => Str::mask($to, '*', 4, -3), 'text' => $text]);

        return 'log-' . Str::uuid();
    }
}
