<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Authenticate
{
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        foreach ($guards as $guard) {
            if (auth($guard)->check()) {
                return $next($request);
            }
        }

        // kein Guard aktiv → Redirect nach Login
        if ($request->is('admin/*')) {
            return redirect()->route('admin.login');
        }

        if ($request->is('customer/*')) {
            return redirect()->route('customer.login');
        }

        return redirect()->route('home'); // Fallback
    }
}
