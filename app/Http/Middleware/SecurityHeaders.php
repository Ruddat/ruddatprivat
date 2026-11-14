<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Grundlegende Sicherheits-Header für alle Web-Antworten.
 *
 * Bewusst ohne Content-Security-Policy: Livewire/Alpine und die bestehenden
 * Module nutzen Inline-Skripte; eine CSP wäre ein eigenes Vorhaben.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        $headers = $response->headers;
        $headers->set('X-Content-Type-Options', 'nosniff');
        $headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Eigene Seiten dürfen sich einbetten (Drive-Vorschau), fremde nicht
        if (! $headers->has('X-Frame-Options')) {
            $headers->set('X-Frame-Options', 'SAMEORIGIN');
        }

        $headers->set('Permissions-Policy', 'geolocation=(), payment=(), usb=(), interest-cohort=()');

        if ($request->isSecure() && app()->isProduction()) {
            $headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }
}
