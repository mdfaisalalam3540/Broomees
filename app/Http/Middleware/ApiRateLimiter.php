<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class ApiRateLimiter
{
    protected $limits = [
        'GET' => 120,    // 120 requests per minute for reads
        'POST' => 30,    // 30 requests per minute for writes
        'PUT' => 30,
        'DELETE' => 30,
        'PATCH' => 30,
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();
        $method = $request->method();

        if (!isset($this->limits[$method])) {
            return $next($request);
        }

        $limit = $this->limits[$method];
        $key = 'rate_limit:' . ($token ? hash('sha256', $token) : $request->ip()) . ':' . $method;

        $requests = Cache::get($key, 0);

        if ($requests >= $limit) {
            return response()->json([
                'error' => 'Too many requests',
                'retry_after' => 60
            ], 429)
                ->header('X-RateLimit-Limit', $limit)
                ->header('X-RateLimit-Remaining', 0)
                ->header('X-RateLimit-Reset', time() + 60);
        }

        Cache::put($key, $requests + 1, 60); // 60 seconds

        return $next($request)
            ->header('X-RateLimit-Limit', $limit)
            ->header('X-RateLimit-Remaining', $limit - ($requests + 1));
    }
}