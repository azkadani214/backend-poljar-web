<?php

namespace App\Services\Auth;

use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;

class TokenService
{
    /**
     * Create token for user
     */
    public function createToken(User $user, string $deviceName = 'web'): array
    {
        // Revoke existing tokens for this device
        $this->revokeDeviceTokens($user, $deviceName);

        // Create new token
        $token = $user->createToken($deviceName);

        return [
            'access_token' => $token->plainTextToken,
            'token_type' => 'Bearer',
            'expires_in' => config('api.sanctum.expiration', 1440) * 60, // in seconds
        ];
    }

    /**
     * Revoke all user tokens
     */
    public function revokeAllTokens(User $user): bool
    {
        return $user->tokens()->delete();
    }

    /**
     * Revoke specific device tokens
     */
    public function revokeDeviceTokens(User $user, string $deviceName): bool
    {
        return $user->tokens()
            ->where('name', $deviceName)
            ->delete();
    }

    /**
     * Revoke current token
     */
    public function revokeCurrentToken(User $user): bool
    {
        $token = $user->currentAccessToken();
        return $token ? $token->delete() : false;
    }

    /**
     * Get user from token
     */
    public function getUserFromToken(string $token): ?User
    {
        $accessToken = PersonalAccessToken::findToken($token);

        if (!$accessToken) {
            return null;
        }

        return $accessToken->tokenable;
    }

    /**
     * Check if token is valid
     */
    public function isTokenValid(string $token): bool
    {
        $accessToken = PersonalAccessToken::findToken($token);

        if (!$accessToken) {
            return false;
        }

        // Check if token is expired
        if ($accessToken->expires_at && $accessToken->expires_at->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Refresh token
     */
    public function refreshToken(User $user, string $oldToken): array
    {
        // Revoke old token
        $accessToken = PersonalAccessToken::findToken($oldToken);
        if ($accessToken) {
            $accessToken->delete();
        }

        // Create new token
        return $this->createToken($user, 'web');
    }
}