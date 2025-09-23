<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PortfolioImage extends Model
{
    protected $fillable = ['portfolio_item_id', 'path', 'caption'];

    public function item()
    {
        return $this->belongsTo(PortfolioItem::class);
    }
}
