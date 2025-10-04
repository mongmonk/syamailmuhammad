<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;

/**
 * Application service provider
 *
 * Instrumentasi performa:
 * - Mencatat slow query menggunakan DB::listen pada threshold yang dikonfigurasi via ENV.
 *   ENV:
 *     - SLOW_QUERY_THRESHOLD_MS (default: 100)
 *     - PERF_LOG_CHANNEL (default: daily)
 *     - ENABLE_SLOW_QUERY_LOGGING (default: true)
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force HTTPS scheme jika diaktifkan via ENV
        if ((bool) env('FORCE_HTTPS', false)) {
            URL::forceScheme('https');
        }

        // Instrumentasi slow query
        if ((bool) env('ENABLE_SLOW_QUERY_LOGGING', true)) {
            $threshold = (int) env('SLOW_QUERY_THRESHOLD_MS', 100);
            $channel = (string) env('PERF_LOG_CHANNEL', 'daily');

            DB::listen(function (\Illuminate\Database\Events\QueryExecuted $query) use ($threshold, $channel) {
                $timeMs = (float) $query->time;

                if ($timeMs >= $threshold) {
                    $req = request();
                    $routeName = optional($req->route())->getName();
                    $path = $req->path();
                    $userId = optional(Auth::user())->id;

                    Log::channel($channel)->warning('Slow query detected', [
                        'sql' => $query->sql,
                        'bindings' => $query->bindings,
                        'time_ms' => $timeMs,
                        'connection' => $query->connectionName ?? null,
                        'route' => $routeName,
                        'path' => $path,
                        'user_id' => $userId,
                    ]);
                }
            });
        }

        // Rate limiting konfigurasi
        RateLimiter::for('global', function (Request $request) {
            return [
                Limit::perMinute((int) env('RATE_LIMIT_GLOBAL', 120))
                    ->by($request->ip())
                    ->response(function (Request $request, array $headers) {
                        Log::channel('security')->warning('Rate limit exceeded', [
                            'limiter' => 'global',
                            'ip' => $request->ip(),
                            'path' => $request->path(),
                            'user_id' => optional(Auth::user())->id,
                        ]);
                        return response('Too Many Requests', 429)->withHeaders($headers);
                    }),
            ];
        });

        RateLimiter::for('auth', function (Request $request) {
            return [
                Limit::perMinute((int) env('RATE_LIMIT_AUTH', 10))
                    ->by($request->ip())
                    ->response(function (Request $request, array $headers) {
                        Log::channel('security')->warning('Rate limit exceeded', [
                            'limiter' => 'auth',
                            'ip' => $request->ip(),
                            'path' => $request->path(),
                            'user_id' => optional(Auth::user())->id,
                        ]);
                        return response('Too Many Requests', 429)->withHeaders($headers);
                    }),
            ];
        });

        RateLimiter::for('search', function (Request $request) {
            return [
                Limit::perMinute((int) env('RATE_LIMIT_SEARCH', 60))
                    ->by($request->ip())
                    ->response(function (Request $request, array $headers) {
                        Log::channel('security')->warning('Rate limit exceeded', [
                            'limiter' => 'search',
                            'ip' => $request->ip(),
                            'path' => $request->path(),
                            'user_id' => optional(Auth::user())->id,
                        ]);
                        return response('Too Many Requests', 429)->withHeaders($headers);
                    }),
            ];
        });

        RateLimiter::for('media', function (Request $request) {
            return [
                Limit::perMinute((int) env('RATE_LIMIT_MEDIA', 60))
                    ->by($request->ip())
                    ->response(function (Request $request, array $headers) {
                        Log::channel('security')->warning('Rate limit exceeded', [
                            'limiter' => 'media',
                            'ip' => $request->ip(),
                            'path' => $request->path(),
                            'user_id' => optional(Auth::user())->id,
                        ]);
                        return response('Too Many Requests', 429)->withHeaders($headers);
                    }),
            ];
        });
    }
}
