<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerLimit extends Model
{
    protected $fillable = [
        'customer_id',
        'type',
        'used',
        'max',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function isExceeded(): bool
    {
        return !is_null($this->max) && $this->used >= $this->max;
    }
}