<?php

namespace App\Models\UtilityCosts;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecordedUtilityCost extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'rental_object_id', 'utility_cost_id', 'amount', 'custom_name', 'year', 'distribution_key'];

    // Beziehung zum UtilityCost-Modell
    public function utilityCost()
    {
        return $this->belongsTo(UtilityCost::class);
    }
}
