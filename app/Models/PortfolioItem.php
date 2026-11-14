<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PortfolioItem extends Model
{
    /**
     * Feste Auswahl an Komplexitätsmerkmalen. Nur zuordnen, wenn im Projekt nachgewiesen.
     */
    public const COMPLEXITY_TAGS = [
        'multi_tenancy' => 'Mehrmandantenfähigkeit',
        'large_schema' => 'Umfangreiche Datenbankstrukturen',
        'payments' => 'Zahlungs- und Abrechnungssysteme',
        'external_apis' => 'Externe API-Anbindungen',
        'queues' => 'Hintergrundverarbeitung und Queues',
        'roles' => 'Rollen- und Berechtigungssysteme',
        'migrations' => 'Datenmigrationen',
        'production' => 'Betrieb produktiver Anwendungen',
        'critical_processes' => 'Verarbeitung geschäftskritischer Prozesse',
        'legacy_modernization' => 'Modernisierung von Altsystemen',
    ];

    protected $fillable = [
        'title', 'slug', 'category', 'summary', 'description', 'cover_image', 'badges', 'type', 'cta_link',
        'is_published', 'is_featured', 'sort_order',
        'project_type', 'project_status', 'role', 'challenge', 'technical_challenges', 'architecture',
        'features', 'integrations', 'operations', 'complexity_tags', 'cover_alt', 'cover_variants',
        'seo_title', 'meta_description',
    ];

    protected $casts = [
        'badges' => 'array',
        'features' => 'array',
        'integrations' => 'array',
        'complexity_tags' => 'array',
        'cover_variants' => 'array',
        'is_published' => 'boolean',
        'is_featured' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Öffentliche URL eines gespeicherten Bildpfads. Ältere Einträge speichern teils
     * "/storage/..." (bereits öffentlich), teils relative Pfade auf der public-Disk.
     */
    public static function imageUrl(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        if (str_starts_with($path, '/') || str_starts_with($path, 'http')) {
            return $path;
        }

        return Storage::disk('public')->url($path);
    }

    /** srcset aus einer Varianten-Liste [breite => pfad]. */
    public static function srcset(?array $variants): ?string
    {
        if (empty($variants)) {
            return null;
        }

        return collect($variants)
            ->map(fn ($path, $width) => static::imageUrl($path) . ' ' . $width . 'w')
            ->implode(', ');
    }

    public function getCoverUrlAttribute(): ?string
    {
        // Bevorzugt die größte optimierte Variante
        if (! empty($this->cover_variants)) {
            return static::imageUrl(collect($this->cover_variants)->sortKeys()->last());
        }

        return static::imageUrl($this->cover_image);
    }

    public function getCoverSrcsetAttribute(): ?string
    {
        return static::srcset($this->cover_variants);
    }

    /** @return array<string, string> key => Label der zugeordneten Merkmale */
    public function complexityLabels(): array
    {
        return collect($this->complexity_tags ?? [])
            ->filter(fn ($key) => isset(self::COMPLEXITY_TAGS[$key]))
            ->mapWithKeys(fn ($key) => [$key => self::COMPLEXITY_TAGS[$key]])
            ->all();
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('title');
    }

    public function images()
    {
        return $this->hasMany(PortfolioImage::class)->orderBy('sort_order')->orderBy('id');
    }

    /** Eindeutiger, lesbarer Slug ohne Zufallsanhang. */
    public static function uniqueSlug(string $source, ?int $ignoreId = null): string
    {
        $base = Str::slug($source) ?: 'referenz';
        $slug = $base;
        $i = 2;

        while (static::where('slug', $slug)->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))->exists()) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }

    protected static function booted()
    {
        static::creating(function ($item) {
            if (empty($item->slug)) {
                $item->slug = static::uniqueSlug($item->title);
            }
        });
    }
}
