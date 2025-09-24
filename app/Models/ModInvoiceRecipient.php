<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ModInvoiceRecipient extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'company_name',
        'name',
        'email',
        'address',
        'zip_code',
        'city',
        'country',
        'customer_type',
        'vat_number',
        'payment_terms',
        'is_active',
        'notes',
        'is_e_invoice',
        'e_invoice_format',
        'delivery_method',
        'invoice_language',
        'iban',
        'bic',
        'default_currency',
        'last_invoice_date',
        'total_invoiced',
        'newsletter_opt_in',
        'customer_id'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
        'is_e_invoice' => 'boolean',
        'newsletter_opt_in' => 'boolean',
        'last_invoice_date' => 'date',
        'total_invoiced' => 'decimal:2',
    ];


public function customer()
{
    return $this->belongsTo(\App\Models\Customer::class, 'customer_id');
}
public function invoices()
{
    return $this->hasMany(ModInvoice::class, 'recipient_id');
}


}