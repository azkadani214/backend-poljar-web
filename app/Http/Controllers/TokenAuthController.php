<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class TokenAuthController extends Controller
{
    /**
     * The secret token from environment
     */
    protected function getSecretToken(): string
    {
        return config('app.admin_token', env('ADMIN_TOKEN', 'admin-secret-token'));
    }

    /**
     * Show the token form
     */
    public function showTokenForm()
    {
        // If already verified, redirect to admin
        if (Session::get('admin_token_verified')) {
            return redirect('/admin');
        }

        return view('auth.token-gate', [
            'title' => 'Admin Token Verification',
            'message' => 'Please enter the admin access token to continue.',
        ]);
    }

    /**
     * Verify the token
     */
    public function verifyToken(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        $token = $request->input('token');

        if ($token === $this->getSecretToken()) {
            Session::put('admin_token_verified', true);
            Session::put('admin_token_verified_at', now());

            return redirect()->intended('/admin')->with('success', 'Token verified successfully');
        }

        return back()->withErrors([
            'token' => 'Invalid token. Please try again.',
        ]);
    }

    /**
     * Logout token session
     */
    public function logoutToken()
    {
        Session::forget('admin_token_verified');
        Session::forget('admin_token_verified_at');

        return redirect()->route('admin.token.login')
            ->with('success', 'You have been logged out from the admin area.');
    }
}
