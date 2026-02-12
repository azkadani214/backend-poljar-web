<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateAdminToken
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if token session exists and is valid
        if (!$this->hasValidTokenSession($request)) {
            return redirect()->route('admin.token.login')
                ->with('error', 'Please enter admin access token first');
        }

        return $next($request);
    }

    /**
     * Check if valid token session exists
     */
    private function hasValidTokenSession(Request $request): bool
    {
        if (!session()->has('admin_token_verified')) {
            return false;
        }

        if (session()->get('admin_token_verified') !== true) {
            return false;
        }

        // Check session lifetime
        $verifiedAt = session()->get('admin_token_verified_at');

        if ($verifiedAt && is_int($verifiedAt)) {
            $lifetime = (int) config('app.admin_token_session_lifetime', 1440); // minutes
            $expiresAt = $verifiedAt + ($lifetime * 60); // Convert to seconds

            if (time() > $expiresAt) {
                session()->forget([
                    'admin_token_verified',
                    'admin_token_verified_at',
                    'admin_token_ip',
                ]);
                return false;
            }
        }

        // Optional: Check if IP changed (security)
        $sessionIp = session()->get('admin_token_ip');
        $currentIp = $request->ip();

        if ($sessionIp && $sessionIp !== $currentIp) {
            session()->forget([
                'admin_token_verified',
                'admin_token_verified_at',
                'admin_token_ip',
            ]);
            return false;
        }

        return true;
    }
}