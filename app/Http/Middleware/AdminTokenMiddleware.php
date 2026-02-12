<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class AdminTokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if token is verified in session
        if (!Session::get('admin_token_verified')) {
            // Redirect to token gate
            return redirect()->route('admin.token.login')
                ->with('error', 'Please verify your admin access token first.');
        }

        return $next($request);
    }
}
