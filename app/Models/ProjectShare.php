<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ProjectShare extends Model
{
    protected $fillable = [
        'shareable_type',
        'shareable_id',
        'token',
        'permission',
        'password_hash',
        'expires_at',
        'is_active',
        'created_by',
        'created_by_admin_id',
        'last_opened_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'last_opened_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function shareable(): MorphTo
    {
        return $this->morphTo();
    }

    public function isExpired(): bool
    {
        return $this->expires_at && now()->greaterThan($this->expires_at);
    }

    public function isUsable(): bool
    {
        return $this->is_active && ! $this->isExpired();
    }
}
