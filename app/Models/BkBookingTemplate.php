<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BkBookingTemplate extends Model
{
    protected $fillable = [
        'tenant_id',
        'name',
        'debit_account_id',
        'credit_account_id',
        'vat_rate',
        'with_vat',
        'description',
        'receipt_type',
        'is_global'
    ];

    protected $casts = [
        'with_vat' => 'boolean',
        'is_global' => 'boolean',
        'vat_rate' => 'decimal:2'
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function debitAccount()
    {
        return $this->belongsTo(Account::class, 'debit_account_id');
    }

    public function creditAccount()
    {
        return $this->belongsTo(Account::class, 'credit_account_id');
    }
}
