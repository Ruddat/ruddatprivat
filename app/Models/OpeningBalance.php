<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OpeningBalance extends Model
{
    protected $fillable = ['fiscal_year_id', 'account_id', 'amount'];
}
