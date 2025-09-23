<?php

namespace App\Http\Middleware;

class RedirectIfNotAdmin
{
    protected function redirectTo($request): ?string
    {
        if (! $request->expectsJson()) {
            return route('admin.login');
        }

        return null;
    }
}
