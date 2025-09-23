<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $fillable = ['tenant_id', 'number', 'name', 'type'];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
