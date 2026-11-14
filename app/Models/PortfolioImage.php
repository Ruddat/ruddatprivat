<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PortfolioImage extends Model
{
    protected $fillable = ['portfolio_item_id', 'path', 'caption', 'alt', 'sort_order', 'width', 'height', 'variants'];

    protected $casts = [
        'variants' => 'array',
        'sort_order' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
    ];

    public function item()
    {
        return $this->belongsTo(PortfolioItem::class, 'portfolio_item_id');
    }

    public function getUrlAttribute(): ?string
    {
        return PortfolioItem::imageUrl($this->path);
    }

    public function getSrcsetAttribute(): ?string
    {
        return PortfolioItem::srcset($this->variants);
    }
}
