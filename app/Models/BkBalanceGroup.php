<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BkBalanceGroup extends Model
{
    protected $table = 'bk_balance_groups';

    protected $fillable = [
        'skr',
        'side',
        'account_number_from',
        'account_number_to',
        'group_key',
        'group_label',
    ];
}