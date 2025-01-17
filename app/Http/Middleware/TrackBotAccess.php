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
        Log::info($request->header('User-Agent'));
        if ($request->header('User-Agent') === 'YourBotName') {
            BotAccessLog::create([
                'bot_name' => 'YourBotName',
                'ip_address' => $request->ip(),
                'url' => $request->fullUrl(),
                'accessed_at' => now(),
            ]);
        }

        return $next($request);
    }
}
