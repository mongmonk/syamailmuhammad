<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

/**
 * TrustProxies middleware
 *
 * Mengonfigurasi proxy tepercaya untuk membaca header X-Forwarded-*
 * agar URL::forceScheme('https') (lihat [AppServiceProvider.boot()](app/Providers/AppServiceProvider.php:39))
 * dan HSTS (lihat [SecurityHeaders.handle()](app/Http/Middleware/SecurityHeaders.php:45)) berfungsi di belakang reverse proxy.
 *
 * Konfigurasi:
 * - ENV TRUSTED_PROXIES: daftar IP/hostname dipisah koma, atau '*' untuk semua.
 *   Contoh:
 *     TRUSTED_PROXIES=*
 *     TRUSTED_PROXIES=127.0.0.1,10.0.0.0/8
 */
class TrustProxies extends Middleware
{
    /**
     * The trusted proxies for this application.
     *
     * @var array|string|null
     */
    protected $proxies = '*';

    /**
     * The headers to be used to detect proxies.
     *
     * Menggunakan semua header standar X-Forwarded-* termasuk proto.
     *
     * @var int
     */
    protected $headers = Request::HEADER_X_FORWARDED_FOR
        | Request::HEADER_X_FORWARDED_HOST
        | Request::HEADER_X_FORWARDED_PORT
        | Request::HEADER_X_FORWARDED_PROTO
        | Request::HEADER_X_FORWARDED_AWS_ELB;

    public function __construct()
    {
        $env = env('TRUSTED_PROXIES', null);

        if (is_string($env) && $env !== '') {
            $envTrim = trim($env);
            if ($envTrim === '*') {
                $this->proxies = '*';
            } elseif (str_contains($envTrim, ',')) {
                $this->proxies = array_map('trim', explode(',', $envTrim));
            } else {
                $this->proxies = $envTrim;
            }
        }
    }
}