<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Helpers\ResponseHelper;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user();

        if (!$user) {
            return ResponseHelper::unauthorized('Unauthorized');
        }

        // Administrator role always has access
        if ($user->roles->contains('name', 'administrator')) {
            return $next($request);
        }

        if (!$user->hasPermission($permission)) {
            return ResponseHelper::forbidden('You do not have permission to perform this action');
        }

        return $next($request);
    }
}
