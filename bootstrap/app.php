<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Treat frontend requests from Nuxt (SPA) as stateful for Sanctum
        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);
        
        // Disable CSRF protection for API routes and the Sanctum CSRF endpoint.
        // Our Nuxt frontend talks to Laravel via /api/* and uses Sanctum tokens,
        // so validating the CSRF token here is not required.
        $middleware->validateCsrfTokens(except: [
            'sanctum/csrf-cookie',
            'api/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
