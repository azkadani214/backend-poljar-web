<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class TrackApiUsage
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);

        $response = $next($request);

        $endTime = microtime(true);
        $duration = round(($endTime - $startTime) * 1000, 2); // in milliseconds

        // Log API usage
        $this->logApiUsage($request, $response, $duration);

        // Add response time header
        if ($response instanceof Response) {
            $response->headers->set('X-Response-Time', $duration . 'ms');
        }

        return $response;
    }

    /**
     * Log API usage details
     */
    private function logApiUsage(Request $request, Response $response, float $duration): void
    {
        $user = $request->user();

        $logData = [
            'method' => $request->method(),
            'path' => $request->path(),
            'status' => $response->getStatusCode(),
            'duration' => $duration,
            'ip' => $request->ip(),
            'user_id' => $user?->id,
            'user_email' => $user?->email,
            'user_agent' => $request->userAgent(),
            'timestamp' => now()->toDateTimeString(),
        ];

        // Log to file (you can change this to database if needed)
        if (config('api.track_usage', false)) {
            Log::channel('api')->info('API Request', $logData);
        }
    }
}