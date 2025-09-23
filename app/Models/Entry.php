<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Entry extends Model
{
    protected $fillable = [
        'tenant_id', 'fiscal_year_id', 'booking_date',
        'debit_account_id', 'credit_account_id',
        'amount', 'description', 'receipt_id',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function debitAccount()
    {
        return $this->belongsTo(\App\Models\Account::class, 'debit_account_id');
    }

    public function creditAccount()
    {
        return $this->belongsTo(\App\Models\Account::class, 'credit_account_id');
    }
}
