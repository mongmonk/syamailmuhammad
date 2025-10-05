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
        // Standardisasi respons 401 untuk request JSON/non-JSON yang tidak terautentikasi
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'code' => 'UNAUTHENTICATED',
                    'message' => trans('errors.UNAUTHENTICATED'),
                ], 401);
            }
            // Untuk request non-JSON (SSR/Blade), render halaman error 401 yang konsisten
            if (view()->exists('errors.401')) {
                return response()->view('errors.401', [
                    'message' => trans('errors.UNAUTHENTICATED'),
                ], 401);
            }
            // Fallback terakhir: teks sederhana
            return response(trans('errors.UNAUTHENTICATED'), 401);
        });

        // Tangani HttpException 401/403 yang berasal dari abort() dalam middleware/web agar tetap konsisten tampilan
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, \Illuminate\Http\Request $request) {
            $status = $e->getStatusCode();

            if ($status === 401) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'code' => 'UNAUTHENTICATED',
                        'message' => trans('errors.UNAUTHENTICATED'),
                    ], 401);
                }
                if (view()->exists('errors.401')) {
                    return response()->view('errors.401', [
                        'message' => trans('errors.UNAUTHENTICATED'),
                    ], 401);
                }
            }

            if ($status === 403) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'code' => 'ACCESS_DENIED',
                        'message' => trans('errors.ACCESS_DENIED'),
                    ], 403);
                }
                if (view()->exists('errors.403')) {
                    return response()->view('errors.403', [], 403);
                }
            }

            // biarkan default handler untuk status lainnya
            return null;
        });
    })->create();
