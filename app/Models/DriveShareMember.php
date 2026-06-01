<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DriveShareMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'share_id',
        'user_id',
        'can_view',
        'can_download',
        'can_upload',
        'can_delete',
    ];

    protected $casts = [
        'can_view' => 'boolean',
        'can_download' => 'boolean',
        'can_upload' => 'boolean',
        'can_delete' => 'boolean',
    ];

    public function share(): BelongsTo
    {
        return $this->belongsTo(DriveShare::class, 'share_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
