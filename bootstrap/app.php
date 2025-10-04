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
        // Global middleware: percaya header X-Forwarded-* dari proxy tepercaya
        $middleware->use([\App\Http\Middleware\TrustProxies::class]);

        $middleware->alias([
            'cache.headers' => \App\Http\Middleware\CacheHeaders::class,
            'security.headers' => \App\Http\Middleware\SecurityHeaders::class,
            // AuthZ aliases
            'ensure.active' => \App\Http\Middleware\EnsureUserActive::class,
            'not.banned' => \App\Http\Middleware\EnsureUserNotBanned::class,
            'role.admin' => \App\Http\Middleware\EnsureRoleAdmin::class,
            // JWT API auth
            'jwt' => \App\Http\Middleware\JwtAuthenticate::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Standardisasi respons 401 untuk request JSON yang tidak terautentikasi
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'code' => 'UNAUTHENTICATED',
                    'message' => trans('errors.UNAUTHENTICATED'),
                ], 401);
            }
            // Untuk request non-JSON, kembalikan 401 dengan pesan sederhana
            return response(trans('errors.UNAUTHENTICATED'), 401);
        });
    })->create();
