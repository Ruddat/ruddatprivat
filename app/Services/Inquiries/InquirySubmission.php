<?php

namespace App\Services\Inquiries;

use App\Events\InquiryReceived;
use App\Models\Inquiry;
use Illuminate\Support\Facades\DB;

/**
 * Speichert eine validierte Anfrage und löst danach die Benachrichtigung aus.
 *
 * - Identische Anfragen (gleiche E-Mail + gleicher Inhalt) innerhalb des
 *   Duplikat-Fensters werden nicht erneut gespeichert und lösen keinen
 *   weiteren Alarm aus.
 * - Das Event wird erst nach erfolgreichem Commit verteilt.
 */
class InquirySubmission
{
    public const DUPLICATE_WINDOW_MINUTES = 30;

    /**
     * @param  array{name: string, company: string, email: string, phone: ?string, type: string, application_summary: string, requirements: ?string}  $data
     * @return array{inquiry: Inquiry, duplicate: bool}
     */
    public function submit(array $data, ?string $ip = null): array
    {
        $fingerprint = Inquiry::fingerprintFor($data['email'], $data['application_summary'], $data['requirements'] ?? null);

        $existing = Inquiry::where('fingerprint', $fingerprint)
            ->where('created_at', '>=', now()->subMinutes(self::DUPLICATE_WINDOW_MINUTES))
            ->latest('id')
            ->first();

        if ($existing) {
            return ['inquiry' => $existing, 'duplicate' => true];
        }

        $inquiry = DB::transaction(function () use ($data, $ip, $fingerprint) {
            $inquiry = Inquiry::create([
                'name' => $data['name'],
                'company' => $data['company'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?: null,
                'type' => $data['type'],
                'application_summary' => $data['application_summary'],
                'requirements' => $data['requirements'] ?: null,
                'privacy_accepted_at' => now(),
                'ip_hash' => $ip ? hash_hmac('sha256', $ip, (string) config('app.key')) : null,
                'fingerprint' => $fingerprint,
            ]);

            InquiryReceived::dispatch($inquiry);

            return $inquiry;
        });

        return ['inquiry' => $inquiry, 'duplicate' => false];
    }
}
