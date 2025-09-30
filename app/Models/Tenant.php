<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    protected $fillable = [
        'name', 'slug', 'email', 'phone', 'street', 'house_number', 'zip', 'city', 'country',
        'tax_number', 'vat_id', 'commercial_register', 'court_register', 'bank_name',
        'iban', 'bic', 'fiscal_year_start', 'currency', 'active', 'is_current', 'customer_id'
    ];

    public static function current()
    {
        return self::where('is_current', true)->first();
    }

    public function accounts()
    {
        return $this->hasMany(Account::class);
    }

    public function fiscalYears()
    {
        return $this->hasMany(FiscalYear::class);
    }

    public function receipts()
    {
        return $this->hasMany(Receipt::class);
    }

    public function entries()
    {
        return $this->hasMany(Entry::class);
    }

public function chartOfAccounts()
{
    return $this->chart_of_accounts ?? 'skr03';
}

}
