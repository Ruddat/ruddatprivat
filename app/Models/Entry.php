<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Entry extends Model
{
    protected $fillable = [
        'tenant_id',
        'fiscal_year_id',
        'booking_date',
        'debit_account_id',
        'credit_account_id',
        'amount',
        'description',
        'receipt_id',
        'transaction_id',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'amount' => 'decimal:2',
    ];

    /**
     * Sollkonto
     */
    public function debitAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'debit_account_id');
    }

    /**
     * Habenkonto
     */
    public function creditAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'credit_account_id');
    }

    /**
     * Alle Buchungen, die zur gleichen Transaktion gehören
     */
    public function transactionEntries(): HasMany
    {
        return $this->hasMany(self::class, 'transaction_id', 'transaction_id');
    }

    /**
     * Geschäftsjahr
     */
    public function fiscalYear(): BelongsTo
    {
        return $this->belongsTo(FiscalYear::class);
    }

    /**
     * Tenant / Firma
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

public function receipt()
{
    return $this->belongsTo(\App\Models\BkReceipt::class, 'receipt_id');
}

}
