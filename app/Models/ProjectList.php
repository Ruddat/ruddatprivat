<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectList extends Model
{
    protected $fillable = [
        'project_board_id',
        'title',
        'position',
        'is_done_list',
    ];

    protected $casts = [
        'is_done_list' => 'boolean',
    ];

    public function board(): BelongsTo
    {
        return $this->belongsTo(ProjectBoard::class, 'project_board_id');
    }

    public function cards(): HasMany
    {
        return $this->hasMany(ProjectCard::class)->orderBy('position');
    }
}
