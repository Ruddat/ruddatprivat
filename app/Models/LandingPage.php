<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingPage extends Model
{
    protected $fillable = [
        'slug',
        'title',
        'meta_description',
        'h1',
        'content',
        'template',
        'hero_image',
        'features',
        'faq',
        'published',
    ];

    protected $casts = [
        'features' => 'array',
        'faq' => 'array',
        'published' => 'boolean',
    ];
}
