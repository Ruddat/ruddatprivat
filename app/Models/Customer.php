<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Customer extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'street',
        'house_number',
        'zip',
        'city',
        'phone',
        'plan',
        'onboarding_done',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];


    protected $casts = [
        'scheduled_for_deletion' => 'boolean',
        'deletion_date' => 'datetime',
    ];


    // ✅ Relation zu Limits
    public function limits()
    {
        return $this->hasMany(CustomerLimit::class);
    }

    // ✅ Ein einzelnes Limit abfragen
    public function limit(string $type): ?CustomerLimit
    {
        return $this->limits()->where('type', $type)->first();
    }

    // ✅ Prüfen ob Kunde das Limit überschreiten würde
    public function canUse(string $type): bool
    {
        $limit = $this->limit($type);
        return $limit ? !$limit->isExceeded() : true; // true = kein Limit
    }

    // ✅ Limit erhöhen (z. B. neue Rechnung angelegt)
    public function incrementLimit(string $type, int $amount = 1): void
    {
        $limit = $this->limit($type);

        if ($limit) {
            $limit->increment('used', $amount);
        } else {
            $this->limits()->create([
                'type' => $type,
                'used' => $amount,
                'max'  => null, // Standard: unlimited
            ]);
        }
    }
}
