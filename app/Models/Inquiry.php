<?php

namespace App\Models;

use App\Enums\InquiryStatus;
use App\Enums\InquiryType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Inquiry extends Model
{
    protected $fillable = [
        'name', 'company', 'email', 'phone', 'type', 'application_summary', 'requirements',
        'privacy_accepted_at', 'status', 'internal_notes', 'status_changed_at', 'first_viewed_at',
        'archived_at', 'ip_hash', 'fingerprint',
    ];

    protected $casts = [
        'type' => InquiryType::class,
        'status' => InquiryStatus::class,
        'privacy_accepted_at' => 'datetime',
        'status_changed_at' => 'datetime',
        'first_viewed_at' => 'datetime',
        'archived_at' => 'datetime',
    ];

    protected $attributes = [
        'status' => 'new',
    ];

    protected static function booted(): void
    {
        static::created(function (Inquiry $inquiry) {
            $inquiry->reference = sprintf('RT-%s-%04d', $inquiry->created_at->format('Y'), $inquiry->id);
            $inquiry->saveQuietly();
        });
    }

    /**
     * Fingerabdruck für die Duplikaterkennung: gleiche E-Mail + gleicher Inhalt.
     */
    public static function fingerprintFor(string $email, string $summary, ?string $requirements): string
    {
        $normalize = fn (?string $v) => Str::of((string) $v)->lower()->squish()->value();

        return hash('sha256', $normalize($email) . '|' . $normalize($summary) . '|' . $normalize($requirements));
    }

    public function alerts()
    {
        return $this->hasMany(InquiryAlert::class)->latest('id');
    }

    public function changeStatus(InquiryStatus $status): void
    {
        if ($this->status === $status) {
            return;
        }

        $this->status = $status;
        $this->status_changed_at = now();
        $this->archived_at = $status === InquiryStatus::Archived ? now() : null;
        $this->save();
    }

    public function markViewed(): void
    {
        if ($this->first_viewed_at === null) {
            $this->forceFill(['first_viewed_at' => now()])->saveQuietly();
        }
    }

    public function isNew(): bool
    {
        return $this->status === InquiryStatus::New;
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->whereIn('status', array_map(fn ($s) => $s->value, InquiryStatus::open()));
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        $like = '%' . str_replace(['%', '_'], ['\%', '\_'], trim($term)) . '%';

        return $query->where(function (Builder $q) use ($like) {
            $q->where('reference', 'like', $like)
                ->orWhere('name', 'like', $like)
                ->orWhere('company', 'like', $like)
                ->orWhere('email', 'like', $like)
                ->orWhere('application_summary', 'like', $like);
        });
    }
}
