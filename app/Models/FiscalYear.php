<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FiscalYear extends Model
{
    protected $fillable = ['tenant_id', 'year', 'start_date', 'end_date', 'closed', 'is_current'];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function entries(): HasMany
    {
        return $this->hasMany(Entry::class);
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
