<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ModIntakeForm extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'services',
        'timeline',
        'budget',
        'referral',
        'project_details',
        'additional_comments',
        'ip_address',
    ];

    protected $casts = [
        'services' => 'array',
    ];
}
