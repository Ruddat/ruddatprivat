<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureCustomerOnboarded
{
    /**
     * Handle an incoming request.
     */
public function handle(Request $request, Closure $next)
{
    $user = Auth::guard('customer')->user();

    if ($user && !$user->onboarding_done) {
        if (!$request->is('customer/onboarding*')) {
            session()->put('needs_onboarding', true); // persistenter
        }
    } else {
        session()->forget('needs_onboarding');
    }

    return $next($request);
}
}