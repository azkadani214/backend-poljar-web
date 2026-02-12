<?php
// ============================================================================
// FILE 94: app/Http/Controllers/Api/V1/Auth/AuthController.php
// ============================================================================

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\Auth\AuthService;
use App\Helpers\ResponseHelper;
use App\Http\Resources\V1\User\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function __construct(
        private AuthService $authService
    ) {}

    /**
     * Login user
     * 
     * @group Authentication
     * @bodyParam email string required User email. Example: admin@polinema.ac.id
     * @bodyParam password string required User password. Example: password123
     * @bodyParam device_name string optional Device name. Example: web
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->login($request->validated());

        return ResponseHelper::success(
            [
                'user' => new UserResource($result['user']),
                'token' => $result['token'],
                'message' => $result['message'],
            ],
            'Login successful'
        );
        } catch (\Exception $e) {
            Log::error('Login error: ' . $e->getMessage(), [
                'exception' => $e,
                'email' => $request->email
            ]);
            return ResponseHelper::error(
                $e->getMessage(),
                $e instanceof \App\Exceptions\Api\ApiException ? $e->getStatusCode() : 500
            );
        }
    }

    /**
     * Logout user
     * 
     * @group Authentication
     * @authenticated
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $result = $this->authService->logout($request->user());

            return ResponseHelper::success(
                $result,
                'Logout successful'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Get authenticated user
     * 
     * @group Authentication
     * @authenticated
     */
    public function me(Request $request): JsonResponse
    {
        try {
            $user = $this->authService->me($request->user());

            return ResponseHelper::success(
                new UserResource($user),
                'User retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Refresh token
     * 
     * @group Authentication
     * @authenticated
     */
    public function refresh(Request $request): JsonResponse
    {
        try {
            $currentToken = $request->bearerToken();
            
            $result = $this->authService->refreshToken(
                $request->user(),
                $currentToken
            );

            return ResponseHelper::success(
                $result,
                'Token refreshed successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Logout from all devices
     * 
     * @group Authentication
     * @authenticated
     */
    public function logoutAll(Request $request): JsonResponse
    {
        try {
            $result = $this->authService->logoutFromAllDevices($request->user());

            return ResponseHelper::success(
                $result,
                'Logged out from all devices successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Verify email
     * 
     * @group Authentication
     * @authenticated
     */
    public function verifyEmail(Request $request): JsonResponse
    {
        try {
            $result = $this->authService->verifyEmail($request->user()->id);

            return ResponseHelper::success(
                $result,
                'Email verified successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }
}
