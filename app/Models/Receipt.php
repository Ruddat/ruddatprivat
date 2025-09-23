<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    protected $fillable = ['tenant_id', 'path', 'date', 'total', 'vendor', 'vat', 'parsed_fields', 'status'];

    protected $casts = ['parsed_fields' => 'array'];
}
