<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class CacheResponse
{
    /**
     * Cache duration in seconds (1 hour for public pages)
     */
    protected int $cacheDuration = 3600;

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ?int $duration = null): Response
    {
        // Only cache GET requests
        if (!$request->isMethod('GET')) {
            return $next($request);
        }

        // Don't cache authenticated users
        if (auth()->check()) {
            return $next($request);
        }

        // Don't cache if there are query parameters that might be user-specific
        if ($request->has('nocache')) {
            return $next($request);
        }

        $duration = $duration ?? $this->cacheDuration;
        $cacheKey = $this->getCacheKey($request);

        // Check if cached response exists
        if (Cache::has($cacheKey)) {
            $cached = Cache::get($cacheKey);
            
            return response($cached['content'])
                ->withHeaders($cached['headers'])
                ->header('X-Cache', 'HIT');
        }

        // Get fresh response
        $response = $next($request);

        // Only cache successful responses
        if ($response->isSuccessful() && !$response->isRedirection()) {
            $this->cacheResponse($cacheKey, $response, $duration);
            $response->header('X-Cache', 'MISS');
        }

        return $response;
    }

    /**
     * Generate cache key for request
     */
    protected function getCacheKey(Request $request): string
    {
        $url = $request->fullUrl();
        $acceptLanguage = $request->header('Accept-Language', 'en');
        
        return 'page_cache:' . md5($url . '|' . $acceptLanguage);
    }

    /**
     * Cache the response
     */
    protected function cacheResponse(string $key, Response $response, int $duration): void
    {
        $headers = [];
        foreach ($response->headers->all() as $name => $values) {
            // Skip headers that shouldn't be cached
            if (!in_array(strtolower($name), ['set-cookie', 'cache-control', 'date', 'age'])) {
                $headers[$name] = $values[0] ?? '';
            }
        }

        Cache::put($key, [
            'content' => $response->getContent(),
            'headers' => $headers,
        ], $duration);
    }

    /**
     * Clear cache for specific URL
     */
    public static function forget(string $url): void
    {
        $key = 'page_cache:' . md5($url . '|en');
        Cache::forget($key);
    }

    /**
     * Clear all page cache
     */
    public static function flush(): void
    {
        // This requires cache tagging support (Redis/Memcached)
        // For file cache, you might need a different approach
        Cache::flush();
    }
}
