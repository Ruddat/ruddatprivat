<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BotAccessLog extends Model
{
    /**
     * Die Datenbank-Tabelle, die mit diesem Modell verknüpft ist.
     *
     * @var string
     */
    protected $table = 'bot_access_logs';

    /**
     * Die Felder, die massenweise befüllt werden können.
     *
     * @var array
     */
    protected $fillable = [
        'bot_name',
        'user_agent',
        'ip_address',
        'url',
        'device',
        'platform',
        'platform_version',
        'browser',
        'browser_version',
        'accessed_at',
    ];

    /**
     * Die Casts für bestimmte Attribute.
     *
     * @var array
     */
    protected $casts = [
        'accessed_at' => 'datetime',
    ];

    /**
     * Zugriffsmutator für `accessed_at`.
     *
     * @param  \Illuminate\Support\Carbon|string $value
     * @return string
     */
    public function getAccessedAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->format('d.m.Y H:i:s');
    }

    /**
     * Suchfilter für die Logs (Beispiel).
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string                                $botName
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterByBotName($query, $botName)
    {
        return $query->where('bot_name', 'like', "%{$botName}%");
    }
}
