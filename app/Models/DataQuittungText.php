<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataQuittungText extends Model
{
    protected $table = 'data_quittung_texts';
    protected $fillable = ['customer_id', 'text', 'created_at', 'updated_at']; // customer_id statt user_id

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
