<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class ProjectBoard extends Model
{
    protected $fillable = [
        'user_id',
        'owner_admin_id',
        'title',
        'slug',
        'description',
        'client_name',
        'client_email',
        'is_active',
        'position',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function lists(): HasMany
    {
        return $this->hasMany(ProjectList::class)->orderBy('position');
    }

    public function cards(): HasMany
    {
        return $this->hasMany(ProjectCard::class);
    }

    public function shares(): MorphMany
    {
        return $this->morphMany(ProjectShare::class, 'shareable');
    }

    public function activeShare()
    {
        return $this->morphOne(ProjectShare::class, 'shareable')
            ->where('is_active', true)
            ->latestOfMany();
    }
}
