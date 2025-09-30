<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BkReceipt extends Model
{
    protected $table = 'bk_receipts';

    protected $fillable = [
        'tenant_id',
        'type',
        'number',
        'date',
        'net_amount',
        'vat_amount',
        'gross_amount',
        'currency',
        'file_path',
        'meta',
    ];

    protected $casts = [
        'date' => 'date',
        'meta' => 'array',
    ];

    public function entries(): HasMany
    {
        return $this->hasMany(\App\Models\Entry::class, 'receipt_id');
    }
}