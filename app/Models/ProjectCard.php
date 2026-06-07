<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectCard extends Model
{
    protected $fillable = [
        'project_board_id',
        'project_list_id',
        'assigned_to',
        'title',
        'description',
        'priority',
        'status',
        'due_date',
        'approved_at',
        'position',
    ];

    protected $casts = [
        'due_date' => 'date',
        'approved_at' => 'datetime',
    ];

    public function board(): BelongsTo
    {
        return $this->belongsTo(ProjectBoard::class, 'project_board_id');
    }

    public function list(): BelongsTo
    {
        return $this->belongsTo(ProjectList::class, 'project_list_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(ProjectCardComment::class)->latest();
    }
}
