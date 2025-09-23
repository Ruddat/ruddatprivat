<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FiscalYear extends Model
{
    protected $fillable = ['tenant_id', 'year', 'start_date', 'end_date', 'closed', 'is_current'];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public static function current($tenantId)
    {
        return self::where('tenant_id', $tenantId)
            ->where('is_current', true)
            ->first()
            ?? self::where('tenant_id', $tenantId)
                ->whereDate('start_date', '<=', now())
                ->whereDate('end_date', '>=', now())
                ->first();
    }
}
