<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ModReceipt extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'template_id',
        'amount',
        'date',
        'description',
        'sender',
        'sender_street',
        'sender_house_number',
        'sender_zip',
        'sender_city',
        'sender_phone',
        'sender_email',
        'sender_tax_number',
        'receiver',
        'receiver_street',
        'receiver_house_number',
        'receiver_zip',
        'receiver_city',
        'receiver_phone',
        'receiver_email',
        'tax_percent',
        'tax_amount',
        'number',
        'type',
        'amount_in_words',
        'hash',
        'pdf_path'
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'tax_percent' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function template()
    {
        return $this->belongsTo(ReceiptTemplate::class);
    }
}
