<?php

namespace App\Models\UtilityCosts;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenantPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'tenant_id', 'rental_object_id',
        'year', 'month', 'amount', 'payment_date'
    ];

    public function tenant()
    {
        return $this->belongsTo(UtilityTenant::class, 'tenant_id');
    }

    public function rentalObject()
    {
        return $this->belongsTo(RentalObject::class, 'rental_object_id');
    }
}
