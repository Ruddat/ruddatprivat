<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BotAccessLog extends Model
{

    protected $fillable = ['bot_name', 'ip_address', 'url', 'accessed_at', 'device', 'platform', 'platform_version', 'browser', 'browser_version'];

    protected $casts = [
        'accessed_at' => 'datetime',
    ];

    public function getAccessedAtAttribute($value)
    {
        return $value->format('d.m.Y H:i:s');
    }

}
