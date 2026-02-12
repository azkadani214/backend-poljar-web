<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Services\Auth\PasswordResetService;
use App\Helpers\ResponseHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PasswordResetController extends Controller
{
    public function __construct(
        private PasswordResetService $passwordResetService
    ) {}

    /**
     * Send password reset link
     * 
     * @group Authentication
     * @bodyParam email string required User email. Example: admin@polinema.ac.id
     */
    public function sendResetLink(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ], [
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'email.exists' => 'Email tidak ditemukan'
        ]);

        try {
            $result = $this->passwordResetService->sendResetLink($request->email);

            return ResponseHelper::success(
                $result,
                'Password reset link sent successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Reset password
     * 
     * @group Authentication
     */
    public function reset(ResetPasswordRequest $request): JsonResponse
    {
        try {
            $result = $this->passwordResetService->resetPassword($request->validated());

            return ResponseHelper::success(
                $result,
                'Password has been reset successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Change password (authenticated user)
     * 
     * @group Authentication
     * @authenticated
     */
    public function changePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
            'new_password_confirmation' => 'required|string'
        ], [
            'current_password.required' => 'Password lama harus diisi',
            'new_password.required' => 'Password baru harus diisi',
            'new_password.min' => 'Password baru minimal 8 karakter',
            'new_password.confirmed' => 'Konfirmasi password tidak cocok'
        ]);

        try {
            $this->passwordResetService->changePassword(
                $request->user()->id,
                $request->current_password,
                $request->new_password
            );

            return ResponseHelper::success(
                null,
                'Password changed successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Validate reset token
     * 
     * @group Authentication
     */
    public function validateToken(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required|string',
            'email' => 'required|email|exists:users,email'
        ]);

        try {
            $isValid = $this->passwordResetService->validateResetToken(
                $request->token,
                $request->email
            );

            if ($isValid) {
                return ResponseHelper::success(
                    ['valid' => true],
                    'Token is valid'
                );
            }

            return ResponseHelper::error('Invalid or expired token', 400);
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }
}