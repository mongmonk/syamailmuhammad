<?php

namespace Tests\Unit;

use App\Http\Middleware\CacheHeaders;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tests\TestCase;

class CacheHeadersMiddlewareTest extends TestCase
{
    protected CacheHeaders $middleware;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->middleware = new CacheHeaders();
    }

    /** @test */
    public function it_sets_default_cache_headers_for_get_requests()
    {
        $request = Request::create('/test', 'GET');
        $response = new Response('Test content');

        $result = $this->middleware->handle($request, function () use ($response) {
            return $response;
        });

        $cache = $result->headers->get('Cache-Control');
        $this->assertStringContainsString('public', $cache);
        $this->assertStringContainsString('max-age=3600', $cache);
        $this->assertEquals('public', $result->headers->get('Pragma'));
        $this->assertNotNull($result->headers->get('Expires'));
    }

    /** @test */
    public function it_sets_long_cache_headers_when_specified()
    {
        $request = Request::create('/test', 'GET');
        $response = new Response('Test content');

        $result = $this->middleware->handle($request, function () use ($response) {
            return $response;
        }, 'long');

        $cache = $result->headers->get('Cache-Control');
        $this->assertStringContainsString('public', $cache);
        $this->assertStringContainsString('max-age=86400', $cache);
        $this->assertStringContainsString('immutable', $cache);
        $this->assertEquals('public', $result->headers->get('Pragma'));
        $this->assertNotNull($result->headers->get('Expires'));
    }

    /** @test */
    public function it_sets_short_cache_headers_when_specified()
    {
        $request = Request::create('/test', 'GET');
        $response = new Response('Test content');

        $result = $this->middleware->handle($request, function () use ($response) {
            return $response;
        }, 'short');

        $cache = $result->headers->get('Cache-Control');
        $this->assertStringContainsString('public', $cache);
        $this->assertStringContainsString('s-maxage=600', $cache);
        $this->assertStringContainsString('max-age=0', $cache);
        $this->assertEquals('public', $result->headers->get('Pragma'));
        $this->assertNotNull($result->headers->get('Expires'));
    }

    /** @test */
    public function it_sets_no_cache_headers_when_specified()
    {
        $request = Request::create('/test', 'GET');
        $response = new Response('Test content');

        $result = $this->middleware->handle($request, function () use ($response) {
            return $response;
        }, 'none');

        $cache = $result->headers->get('Cache-Control');
        $this->assertStringContainsString('no-store', $cache);
        $this->assertStringContainsString('no-cache', $cache);
        $this->assertStringContainsString('must-revalidate', $cache);
        $this->assertStringContainsString('max-age=0', $cache);
        $this->assertEquals('no-cache', $result->headers->get('Pragma'));
        $this->assertEquals('Fri, 01 Jan 1990 00:00:00 GMT', $result->headers->get('Expires'));
    }

    /** @test */
    public function it_sets_no_cache_headers_for_non_get_requests()
    {
        $request = Request::create('/test', 'POST');
        $response = new Response('Test content');

        $result = $this->middleware->handle($request, function () use ($response) {
            return $response;
        });

        $cache = $result->headers->get('Cache-Control');
        $this->assertStringContainsString('no-store', $cache);
        $this->assertStringContainsString('no-cache', $cache);
        $this->assertStringContainsString('must-revalidate', $cache);
        $this->assertStringContainsString('max-age=0', $cache);
        $this->assertEquals('no-cache', $result->headers->get('Pragma'));
        $this->assertEquals('Fri, 01 Jan 1990 00:00:00 GMT', $result->headers->get('Expires'));
    }

    /** @test */
    public function it_sets_no_cache_headers_for_error_responses()
    {
        $request = Request::create('/test', 'GET');
        $response = new Response('Error content', 404);

        $result = $this->middleware->handle($request, function () use ($response) {
            return $response;
        });

        // Should not have cache headers for error responses
        $this->assertNull($result->headers->get('Cache-Control'));
        $this->assertNull($result->headers->get('Pragma'));
        $this->assertNull($result->headers->get('Expires'));
    }

    /** @test */
    public function it_sets_expiration_time_correctly()
    {
        $request = Request::create('/test', 'GET');
        $response = new Response('Test content');

        $result = $this->middleware->handle($request, function () use ($response) {
            return $response;
        });

        $expires = $result->headers->get('Expires');
        $expiresTime = strtotime($expires);
        $currentTime = time();
        
        // Expiration time should be approximately 1 hour from now (3600 seconds)
        $this->assertGreaterThan($currentTime + 3500, $expiresTime);
        $this->assertLessThan($currentTime + 3700, $expiresTime);
    }

    /** @test */
    public function it_sets_long_expiration_time_correctly()
    {
        $request = Request::create('/test', 'GET');
        $response = new Response('Test content');

        $result = $this->middleware->handle($request, function () use ($response) {
            return $response;
        }, 'long');

        $expires = $result->headers->get('Expires');
        $expiresTime = strtotime($expires);
        $currentTime = time();
        
        // Expiration time should be approximately 24 hours from now (86400 seconds)
        $this->assertGreaterThan($currentTime + 86300, $expiresTime);
        $this->assertLessThan($currentTime + 86500, $expiresTime);
    }
}