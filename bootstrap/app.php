<?php

use App\Http\Middleware\TrackBotAccess;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {
            // 🔓 Admin-Auth (Login, Logout)
            Route::middleware(['web'])
                ->prefix('admin')
                ->as('admin.')
                ->group(base_path('routes/admin-auth.php'));

            // 🔒 Admin-Bereich
            Route::middleware(['web', 'auth:admin'])
                ->prefix('admin')
                ->as('admin.')
                ->group(base_path('routes/admin.php'));

            // 🔓 Customer-Auth (Login, Logout)
            Route::middleware(['web'])
                ->prefix('customer')
                ->as('customer.')
                ->group(base_path('routes/customer-auth.php'));

            // 🔒 Customer-Bereich
            Route::middleware(['web', 'auth:customer', 'onboarded'])
                ->prefix('customer')
                ->as('customer.')
                ->group(base_path('routes/customer.php'));

            // Öffentlich
            Route::middleware(['web'])
                ->group(base_path('routes/web.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Web-Stack
        $middleware->group('web', [
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
        ]);

        // API-Stack
        $middleware->group('api', [
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \Illuminate\Routing\Middleware\ThrottleRequests::class . ':api',
        ]);

        // Aliases
        $middleware->alias([
            'auth' => \App\Http\Middleware\Authenticate::class,
            'auth.admin' => \App\Http\Middleware\Authenticate::class,
            'auth.customer' => \App\Http\Middleware\Authenticate::class,
            'onboarded' => \App\Http\Middleware\EnsureCustomerOnboarded::class,
        ]);

        // Globale Middleware
        $middleware->append(TrackBotAccess::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
