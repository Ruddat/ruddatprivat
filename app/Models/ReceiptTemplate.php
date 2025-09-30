<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReceiptTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id', 'name', 'type', 'sender_name', 'sender_street',
        'sender_house_number', 'sender_zip', 'sender_city', 'sender_phone',
        'sender_email', 'sender_tax_number', 'receiver_name', 'receiver_street',
        'receiver_house_number', 'receiver_zip', 'receiver_city', 'receiver_phone',
        'receiver_email', 'default_description', 'include_tax', 'tax_percent', 'is_default'
    ];

    protected $casts = [
        'include_tax' => 'boolean',
        'is_default' => 'boolean',
        'tax_percent' => 'decimal:2'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function receipts()
    {
        return $this->hasMany(ModReceipt::class, 'template_id');
    }

    // Scope für bestimmte Template-Typen
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }
}
