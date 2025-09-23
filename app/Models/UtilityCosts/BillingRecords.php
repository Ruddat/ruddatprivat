<?php

namespace App\Models\UtilityCosts;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillingRecords extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'billing_header_id',
        'tenant_id',
        'rental_object_id', // Falls dieses Feld im Schema existiert
        'billing_period',
        'total_cost',
        'prepayment',
        'balance_due',
        'standard_costs',
        'heating_costs',
        'pdf_path',
        'pdf_path_second',
        'pdf_path_third',

    ];

    // Beziehung zu BillingHeader
    public function billingHeader()
    {
        return $this->belongsTo(BillingHeader::class, 'billing_header_id');
    }

    // Beziehung zu Tenant
    public function tenant()
    {
        return $this->belongsTo(UtilityTenant::class, 'tenant_id');
    }

    // Beziehung zu RentalObject
    public function rentalObject()
    {
        return $this->belongsTo(RentalObject::class, 'rental_object_id');
    }
}
