<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PortfolioItem extends Model
{
    protected $fillable = [
        'title', 'slug', 'category', 'summary', 'description', 'cover_image', 'badges', 'type', 'cta_link'
    ];

    protected $casts = [
        'badges' => 'array',
    ];

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
