<?php

namespace App\Http\Middleware;

use App\Models\BotAccessLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Jenssegers\Agent\Agent;

class TrackBotAccess
{
    /**
     * Handle an incoming request.
     */
    public function handle($request, Closure $next)
    {
        // Liste der Bots aus der Config laden
        $trackedBots = config('bots.tracked_bots', [
            'Googlebot', // Beispiel: Googlebot als Teilstring erkennen
            'TurnitinBot',
            'Plesk screenshot bot',
        ]);

        // User-Agent-Header abfragen
        $userAgent = $request->header('User-Agent');
        Log::info("User-Agent: {$userAgent}");

        // Jenssegers Agent initialisieren
        $agent = new Agent;
        $agent->setUserAgent($userAgent);

        // Gerätedetails extrahieren
        $device = $agent->device();
        $platform = $agent->platform();
        $platformVersion = $agent->version($platform);
        $browser = $agent->browser();
        $browserVersion = $agent->version($browser);

        // Prüfen, ob der User-Agent einem bekannten Bot entspricht
        foreach ($trackedBots as $botName) {
            if (stripos($userAgent, $botName) !== false) {
                BotAccessLog::create([
                    'bot_name' => $botName,
                    'user_agent' => $userAgent, // Ganzes User-Agent speichern
                    'ip_address' => $request->ip(),
                    'url' => $request->fullUrl(),
                    'device' => $device,
                    'platform' => $platform,
                    'platform_version' => $platformVersion,
                    'browser' => $browser,
                    'browser_version' => $browserVersion,
                    'accessed_at' => now(),
                ]);

                Log::info("Bot-Zugriff protokolliert: {$botName}");
                break;
            }
        }

        return $next($request);
    }
}
