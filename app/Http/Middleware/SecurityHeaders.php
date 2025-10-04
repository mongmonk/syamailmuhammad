<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class SecurityHeaders
{
    /**
     * Tambah header keamanan standar OWASP untuk semua respons HTTP.
     */
    public function handle(Request $request, Closure $next): SymfonyResponse
    {
        $response = $next($request);

        if (!$response instanceof SymfonyResponse) {
            return $response;
        }

        // Kebijakan default yang aman, dapat dioverride via ENV.
        // Pada lingkungan lokal/DEBUG, longgarkan CSP agar sumber eksternal (fonts & CDN) dapat dimuat.
        $isLocal = app()->environment('local') || (bool) env('APP_DEBUG', false);

        if ($isLocal) {
            // Dev policy: izinkan fonts dan Tailwind CDN yang digunakan di layout
            $csp = "default-src 'self'; img-src 'self' data:; media-src 'self' blob: data:; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.tailwindcss.com; style-src 'self' 'unsafe-inline' https://fonts.bunny.net https://fonts.googleapis.com; style-src-elem 'self' 'unsafe-inline' https://fonts.bunny.net https://fonts.googleapis.com; font-src 'self' data: https://fonts.gstatic.com https://fonts.bunny.net; connect-src 'self' https://fonts.googleapis.com https://fonts.gstatic.com https://fonts.bunny.net";
        } else {
            // Production: gunakan kebijakan dari ENV atau fallback ketat
            $csp = env('CSP_POLICY', "default-src 'self'; img-src 'self' data:; media-src 'self' blob: data:; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; font-src 'self' data:; connect-src 'self'");
        }

        $xFrame = env('X_FRAME_OPTIONS', 'SAMEORIGIN');
        $referrer = env('REFERRER_POLICY', 'strict-origin-when-cross-origin');
        $permissions = env('PERMISSIONS_POLICY', 'camera=(), microphone=(), geolocation=()');
        $coep = env('COEP', null); // opsional, hati-hati dapat memblok third-party
        $corp = env('CORP', 'same-origin');
        $coop = env('COOP', 'same-origin');

        // Header utama keamanan
        $response->headers->set('Content-Security-Policy', $csp);
        $response->headers->set('X-Frame-Options', $xFrame);
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', $referrer);
        $response->headers->set('Permissions-Policy', $permissions);
        $response->headers->set('Cross-Origin-Resource-Policy', $corp);
        $response->headers->set('Cross-Origin-Opener-Policy', $coop);

        if ($coep) {
            $response->headers->set('Cross-Origin-Embedder-Policy', $coep);
        }

        // HSTS hanya aktif saat HTTPS atau FORCE_HTTPS diaktifkan
        if ($request->isSecure() || env('FORCE_HTTPS', false)) {
            $hstsMaxAge = (int) env('HSTS_MAX_AGE', 31536000); // 1 tahun
            $hstsIncludeSubdomains = env('HSTS_INCLUDE_SUBDOMAINS', true) ? '; includeSubDomains' : '';
            $hstsPreload = env('HSTS_PRELOAD', false) ? '; preload' : '';
            $response->headers->set('Strict-Transport-Security', "max-age={$hstsMaxAge}{$hstsIncludeSubdomains}{$hstsPreload}");
        }

        return $response;
    }
}