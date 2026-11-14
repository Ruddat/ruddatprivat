<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InquiryAlert extends Model
{
    public const QUEUED = 'queued';

    public const ACCEPTED = 'accepted';

    public const FAILED = 'failed';

    public const SKIPPED = 'skipped';

    protected $fillable = [
        'inquiry_id', 'channel', 'provider', 'recipient_masked', 'status', 'attempts', 'provider_message_id',
        'last_error', 'is_test', 'is_fallback', 'dedupe_key', 'accepted_at', 'failed_at',
    ];

    protected $casts = [
        'is_test' => 'boolean',
        'is_fallback' => 'boolean',
        'accepted_at' => 'datetime',
        'failed_at' => 'datetime',
    ];

    public function inquiry()
    {
        return $this->belongsTo(Inquiry::class);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::QUEUED => $this->attempts > 0 ? 'Wird erneut versucht' : 'Angestoßen',
            self::ACCEPTED => 'Vom Anbieter angenommen',
            self::FAILED => 'Fehlgeschlagen',
            self::SKIPPED => 'Übersprungen',
            default => $this->status,
        };
    }

    public function statusClasses(): string
    {
        return match ($this->status) {
            self::ACCEPTED => 'bg-emerald-50 text-emerald-800 ring-emerald-200',
            self::FAILED => 'bg-red-50 text-red-800 ring-red-200',
            self::SKIPPED => 'bg-slate-50 text-slate-600 ring-slate-200',
            default => 'bg-amber-50 text-amber-800 ring-amber-200',
        };
    }

    public static function channelLabel(string $channel): string
    {
        return match ($channel) {
            'mail' => 'E-Mail',
            'sms' => 'SMS',
            'telegram' => 'Telegram',
            'whatsapp' => 'WhatsApp Business',
            default => $channel,
        };
    }

    /** Maskiert Telefonnummern / E-Mail-Adressen für die Anzeige. */
    public static function mask(?string $recipient): ?string
    {
        if (blank($recipient)) {
            return null;
        }

        if (str_contains($recipient, '@')) {
            [$local, $domain] = explode('@', $recipient, 2);

            return mb_substr($local, 0, 2) . '…@' . $domain;
        }

        return mb_substr($recipient, 0, 4) . '…' . mb_substr($recipient, -3);
    }
}
