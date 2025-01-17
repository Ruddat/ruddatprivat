<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\BotAccessLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class TrackBotAccess
{
    /**
     * Handle an incoming request.
     */
    public function handle($request, Closure $next)
    {
        // Liste der zu überwachenden Bots
        $trackedBots = [
            'Plesk screenshot bot https://support.plesk.com/hc/en-us/articles/10301006946066',
            'AnotherBot/1.0',
            'CustomBotName',
        ];

        // User-Agent-Header abfragen
        $userAgent = $request->header('User-Agent');
        Log::info("User-Agent: {$userAgent}");

        // Prüfen, ob der User-Agent zu den überwachten Bots gehört
        if (in_array($userAgent, $trackedBots)) {
            BotAccessLog::create([
                'bot_name' => $userAgent, // User-Agent als Bot-Name speichern
                'ip_address' => $request->ip(),
                'url' => $request->fullUrl(),
                'accessed_at' => now(),
            ]);

            Log::info("Bot-Zugriff protokolliert: {$userAgent}");
        }

        return $next($request);
    }
}
