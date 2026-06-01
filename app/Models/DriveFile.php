<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class DriveFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'folder_id',
        'uploaded_by',
        'original_name',
        'stored_name',
        'mime_type',
        'size',
        'disk',
        'path',
        'checksum',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function folder(): BelongsTo
    {
        return $this->belongsTo(DriveFolder::class, 'folder_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getHumanSizeAttribute(): string
    {
        $bytes = (int) $this->size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes >= 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $i === 0 ? 0 : 2).' '.$units[$i];
    }

    public function existsOnDisk(): bool
    {
        return Storage::disk($this->disk)->exists($this->path);
    }
}
