<?php
// ============================================================================
// FILE 107: app/Http/Middleware/ApiVersion.php
// ============================================================================

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiVersion
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Set API version in request attributes
        $version = $this->extractVersion($request);
        $request->attributes->set('api_version', $version);

        // Add version to response headers
        $response = $next($request);
        
        if ($response instanceof Response) {
            $response->headers->set('X-API-Version', $version);
            $response->headers->set('X-API-Available-Versions', 'v1');
        }

        return $response;
    }

    /**
     * Extract API version from request
     */
    private function extractVersion(Request $request): string
    {
        // Get from URL path
        $path = $request->path();
        if (preg_match('/^api\/(v\d+)/', $path, $matches)) {
            return $matches[1];
        }

        // Get from Accept header
        $acceptHeader = $request->header('Accept');
        if (preg_match('/application\/vnd\.api\.(v\d+)\+json/', $acceptHeader, $matches)) {
            return $matches[1];
        }

        // Default version
        return config('api.version', 'v1');
    }
}
