<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class PasswordResetService
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    /**
     * Send password reset link
     */
    public function sendResetLink(string $email): array
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user) {
            throw new \Exception('User not found with this email address');
        }

        $status = Password::sendResetLink(['email' => $email]);

        if ($status !== Password::RESET_LINK_SENT) {
            throw new \Exception('Failed to send password reset link');
        }

        return [
            'message' => 'Password reset link sent to your email',
            'email' => $email,
        ];
    }

    /**
     * Reset password
     */
    public function resetPassword(array $credentials): array
    {
        $status = Password::reset(
            $credentials,
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            throw new \Exception('Failed to reset password');
        }

        return [
            'message' => 'Password has been reset successfully',
        ];
    }

    /**
     * Change password
     */
    public function changePassword(string $userId, string $currentPassword, string $newPassword): bool
    {
        $user = $this->userRepository->findOrFail($userId);

        if (!Hash::check($currentPassword, $user->password)) {
            throw new \Exception('Current password is incorrect');
        }

        return $this->userRepository->updatePassword($userId, $newPassword);
    }

    /**
     * Validate reset token
     */
    public function validateResetToken(string $token, string $email): bool
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user) {
            return false;
        }

        return Password::tokenExists($user, $token);
    }
}