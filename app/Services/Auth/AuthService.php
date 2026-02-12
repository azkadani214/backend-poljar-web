<?php


namespace App\Services\Auth;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Exceptions\Api\UnauthorizedException;
use App\Exceptions\Api\ValidationException;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private TokenService $tokenService
    ) {}

    /**
     * REMOVED: register() method
     * Users can only be created by administrators through UserService
     */

    /**
     * Login user with email and password
     */
    public function login(array $credentials): array
    {
        $user = $this->userRepository->findByEmail($credentials['email']);

        if (!$user) {
            throw new UnauthorizedException('Invalid credentials');
        }

        if (!Hash::check($credentials['password'], $user->password)) {
            throw new UnauthorizedException('Invalid credentials');
        }

        if ($user->status !== 'active') {
            throw new UnauthorizedException('Your account is inactive. Please contact administrator.');
        }

        // Create token
        $tokenData = $this->tokenService->createToken(
            $user,
            $credentials['device_name'] ?? 'web'
        );

        // Update last login
        $user->update(['last_login_at' => now()]);

        // Log activity
        \App\Helpers\ActivityLogger::log('login', 'Auth', "Pengguna {$user->name} berhasil masuk ke sistem.", null, $user->id);

        return [
            'user' => $user->load(['divisions', 'memberships', 'roles.permissions']),
            'token' => $tokenData,
            'message' => 'Login successful',
        ];
    }

    /**
     * Logout user
     */
    public function logout(User $user): array
    {
        $this->tokenService->revokeCurrentToken($user);

        // Log activity
        \App\Helpers\ActivityLogger::log('logout', 'Auth', "Pengguna {$user->name} telah keluar dari sistem.", null, $user->id);

        return [
            'message' => 'Logout successful',
        ];
    }

    /**
     * Logout from all devices
     */
    public function logoutFromAllDevices(User $user): array
    {
        $this->tokenService->revokeAllTokens($user);

        return [
            'message' => 'Logged out from all devices successfully',
        ];
    }

    /**
     * Get authenticated user
     */
    public function me(User $user): User
    {
        return $user->load(['divisions', 'memberships.division', 'memberships.position', 'roles.permissions']);
    }

    /**
     * Refresh token
     */
    public function refreshToken(User $user, string $oldToken): array
    {
        $tokenData = $this->tokenService->refreshToken($user, $oldToken);

        return [
            'token' => $tokenData,
            'message' => 'Token refreshed successfully',
        ];
    }

    /**
     * Verify email
     */
    public function verifyEmail(string $userId): array
    {
        $user = $this->userRepository->findOrFail($userId);

        if ($user->email_verified_at) {
            throw new ValidationException('Email already verified');
        }

        $this->userRepository->verifyEmail($userId);

        return [
            'message' => 'Email verified successfully',
            'user' => $user->fresh(),
        ];
    }

    /**
     * Validate current password
     */
    public function validateCurrentPassword(User $user, string $password): bool
    {
        return Hash::check($password, $user->password);
    }
}
