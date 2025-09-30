<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BwaGroup extends Model
{
    protected $fillable = [
        'skr',
        'account_number_from',
        'account_number_to',
        'group_key',
        'group_label',
    ];
}