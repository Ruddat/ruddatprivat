<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class DriveShare extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'folder_id',
        'name',
        'token',
        'expires_at',
        'can_view',
        'can_download',
        'can_upload',
        'can_delete',
        'is_active',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'can_view' => 'boolean',
        'can_download' => 'boolean',
        'can_upload' => 'boolean',
        'can_delete' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $share): void {
            if (! $share->token) {
                $share->token = Str::random(48);
            }
        });
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function folder(): BelongsTo
    {
        return $this->belongsTo(DriveFolder::class, 'folder_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(DriveShareMember::class, 'share_id');
    }

    public function isUsable(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        return true;
    }
}
