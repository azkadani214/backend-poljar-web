<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Helpers\ResponseHelper;

class CheckApiToken
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(
                ResponseHelper::error('API token required', 401),
                401
            );
        }

        // Additional token validation can be added here
        // For now, Sanctum handles the authentication

        return $next($request);
    }
}
