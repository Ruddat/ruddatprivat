<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ModInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'customer_id',
        'invoice_number',
        'invoice_date',
        'due_date',
        'total_amount',
        'status',
        'notes',
        'pdf_path',
        'creator_id',
        'recipient_id',   // 👈 FEHLT AKTUELL
    ];

    public function recipient()
    {
        return $this->belongsTo(ModInvoiceRecipient::class, 'recipient_id');
    }

    public function items()
    {
        return $this->hasMany(ModInvoiceItem::class, 'invoice_id', 'id');
    }

    public function creator()
    {
        return $this->belongsTo(ModInvoiceCreator::class, 'creator_id');
    }
}