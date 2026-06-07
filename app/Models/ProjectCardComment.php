<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectCardComment extends Model
{
    protected $fillable = [
        'project_card_id',
        'user_id',
        'author_name',
        'comment',
    ];

    public function card(): BelongsTo
    {
        return $this->belongsTo(ProjectCard::class, 'project_card_id');
    }
}
