<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PortfolioItem extends Model
{
    protected $fillable = [
        'title', 'slug', 'category', 'summary', 'description', 'cover_image', 'badges', 'type', 'cta_link',
        'is_published', 'is_featured', 'sort_order',
    ];

    protected $casts = [
        'badges' => 'array',
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

        return \Illuminate\Support\Facades\Storage::disk('public')->url($path);
    }

    public function getCoverUrlAttribute(): ?string
    {
        return static::imageUrl($this->cover_image);
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
        return $this->hasMany(PortfolioImage::class);
    }

    // Slug automatisch setzen
    protected static function booted()
    {
        static::creating(function ($item) {
            if (empty($item->slug)) {
                $item->slug = Str::slug($item->title) . '-' . uniqid();
            }
        });
    }
}
