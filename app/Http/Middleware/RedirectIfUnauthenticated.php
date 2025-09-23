<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfUnauthenticated
{
    public function handle(Request $request, Closure $next, ?string $guard = null): Response
    {
        if (! auth($guard)->check()) {
            // Admin-Bereich
            if ($request->is('admin/*')) {
                return redirect()->route('admin.login');
            }

            // Customer-Bereich
            if ($request->is('customer/*')) {
                return redirect()->route('customer.login');
            }

            // Fallback
            return redirect()->route('home');
        }

        return $next($request);
    }
}
