<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InvoiceUpload extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'invoice_number',
        'invoice_date',
        'net_amount',
        'vat_amount',
        'gross_amount',
        'file_path',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'net_amount'   => 'decimal:2',
        'vat_amount'   => 'decimal:2',
        'gross_amount' => 'decimal:2',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}