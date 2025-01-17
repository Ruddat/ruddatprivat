<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\BotAccessLog;
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
        // Liste der zu überwachenden Bots
        $trackedBots = [
            'Plesk screenshot bot https://support.plesk.com/hc/en-us/articles/10301006946066',
            'AnotherBot/1.0',
            'CustomBotName',
            'TurnitinBot (https://turnitin.com/robot/crawlerinfo.html)', // Neuer Bot hinzugefügt
        ];

        // User-Agent-Header abfragen
        $userAgent = $request->header('User-Agent');
        Log::info("User-Agent: {$userAgent}");

        // Jenssegers Agent initialisieren
        $agent = new Agent();
        $agent->setUserAgent($userAgent);

        // Gerätedetails extrahieren
        $device = $agent->device(); // Gerätetyp (z. B. iPhone)
        $platform = $agent->platform(); // Betriebssystem (z. B. iOS)
        $platformVersion = $agent->version($platform); // OS-Version (z. B. 18.2.1)
        $browser = $agent->browser(); // Browser (z. B. Safari, Chrome)
        $browserVersion = $agent->version($browser); // Browser-Version

        // Prüfen, ob der User-Agent zu den überwachten Bots gehört
        if (in_array($userAgent, $trackedBots)) {
            BotAccessLog::create([
                'bot_name' => $userAgent, // User-Agent als Bot-Name speichern
                'ip_address' => $request->ip(),
                'url' => $request->fullUrl(),
                'device' => $device, // Neues Feld für Gerät
                'platform' => $platform, // Neues Feld für OS
                'platform_version' => $platformVersion, // Neues Feld für OS-Version
                'browser' => $browser, // Neues Feld für Browser
                'browser_version' => $browserVersion, // Neues Feld für Browser-Version
                'accessed_at' => now(),
            ]);

            Log::info("Bot-Zugriff protokolliert: {$userAgent}");
        }

        return $next($request);
    }
}
