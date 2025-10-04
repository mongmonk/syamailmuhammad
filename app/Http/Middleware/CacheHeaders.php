<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class CacheHeaders
{
    /**
     * Default cache TTL for public responses (in seconds)
     */
    private $defaultTtl = 3600; // 1 hour

    /**
     * Long cache TTL for static content (in seconds)
     */
    private $longTtl = 86400; // 24 hours

    /**
     * Short cache TTL for dynamic content (in seconds)
     */
    private $shortTtl = 600; // 10 minutes

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string|null  $cacheType
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, ?string $cacheType = null)
    {
        $response = $next($request);

        if (!$response instanceof SymfonyResponse) {
            return $response;
        }

        // Don't add cache headers to error responses
        if ($response->getStatusCode() >= 400) {
            // Bersihkan header cache bawaan (Symfony Response default: "no-cache, private")
            $this->clearCacheHeaders($response);
            return $response;
        }

        // Don't add cache headers to non-GET requests
        if (!$request->isMethod('GET')) {
            // Pastikan header cache di-reset agar nilai tepat sesuai ekspektasi test
            $this->clearCacheHeaders($response);
            // Set sebagai satu string agar urutan tepat
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
            // Hapus directive "private" jika disisipkan oleh Symfony
            if (method_exists($response->headers, 'removeCacheControlDirective')) {
                $response->headers->removeCacheControlDirective('private');
            }
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', 'Fri, 01 Jan 1990 00:00:00 GMT');
            return $response;
        }

        // Apply cache headers based on cache type
        switch ($cacheType) {
            case 'long':
                $this->setLongCacheHeaders($response);
                break;
            case 'short':
                $this->setShortCacheHeaders($response);
                break;
            case 'none':
                $this->setNoCacheHeaders($response);
                break;
            default:
                $this->setDefaultCacheHeaders($response);
                break;
        }

        return $response;
    }

    /**
     * Set default cache headers
     *
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @return void
     */
    private function setDefaultCacheHeaders($response)
    {
        // Clear to avoid implicit directives and ensure order
        $this->clearCacheHeaders($response);
        // Set sebagai satu string agar urutan tepat: "public, max-age=..."
        $response->headers->set('Cache-Control', "public, max-age={$this->defaultTtl}");
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Expires', $this->getExpirationTime($this->defaultTtl));
    }

    /**
     * Set long cache headers for static content
     *
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @return void
     */
    private function setLongCacheHeaders($response)
    {
        // Clear first to ensure order/content
        $this->clearCacheHeaders($response);
        // Set sebagai satu string agar urutan tepat: "public, max-age=..., immutable"
        $response->headers->set('Cache-Control', "public, max-age={$this->longTtl}, immutable");
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Expires', $this->getExpirationTime($this->longTtl));
    }

    /**
     * Set short cache headers for dynamic content
     *
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @return void
     */
    private function setShortCacheHeaders($response)
    {
        // Clear first to ensure exact expected header value and order
        $this->clearCacheHeaders($response);
        // Set sebagai satu string agar urutan tepat: "public, s-maxage=..., max-age=0"
        $response->headers->set('Cache-Control', "public, s-maxage={$this->shortTtl}, max-age=0");
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Expires', $this->getExpirationTime($this->shortTtl));
    }

    /**
     * Set no-cache headers
     *
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @return void
     */
    private function setNoCacheHeaders($response)
    {
        // Pastikan tidak ada header cache lain yang tertinggal
        $this->clearCacheHeaders($response);
        // Set sebagai satu string agar urutan tepat
        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        // Hapus directive "private" jika disisipkan oleh Symfony
        if (method_exists($response->headers, 'removeCacheControlDirective')) {
            $response->headers->removeCacheControlDirective('private');
        }
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', 'Fri, 01 Jan 1990 00:00:00 GMT');
    }

    /**
     * Hapus header terkait cache agar tidak mengganggu nilai yang diharapkan test.
     *
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @return void
     */
    private function clearCacheHeaders($response)
    {
        $response->headers->remove('Cache-Control');
        $response->headers->remove('Pragma');
        $response->headers->remove('Expires');
    }

    /**
     * Get expiration time in HTTP date format
     *
     * @param int $seconds
     * @return string
     */
    private function getExpirationTime($seconds)
    {
        return gmdate('D, d M Y H:i:s T', time() + $seconds);
    }
}