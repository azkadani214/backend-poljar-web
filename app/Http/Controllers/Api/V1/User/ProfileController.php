<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateProfileRequest;
use App\Services\User\ProfileService;
use App\Http\Resources\V1\User\UserDetailResource;
use App\Helpers\ResponseHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function __construct(
        private ProfileService $profileService
    ) {}

    /**
     * Get user profile
     * 
     * @group Profile
     * @authenticated
     */
    public function show(Request $request): JsonResponse
    {
        try {
            $profile = $this->profileService->getProfile($request->user());

            return ResponseHelper::success(
                new UserDetailResource($profile),
                'Profile retrieved successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Update profile
     * 
     * @group Profile
     * @authenticated
     */
    public function update(UpdateProfileRequest $request): JsonResponse
    {
        try {
            $profile = $this->profileService->updateProfile(
                $request->user(),
                $request->validated()
            );

            return ResponseHelper::updated(
                new UserDetailResource($profile),
                'Profile updated successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Change password
     * 
     * @group Profile
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
            $this->profileService->changePassword(
                $request->user(),
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
     * Update profile photo
     * 
     * @group Profile
     * @authenticated
     */
    public function updatePhoto(Request $request): JsonResponse
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpg,jpeg,png,webp|max:1024'
        ]);

        try {
            $result = $this->profileService->updateProfilePhoto(
                $request->user(),
                $request->file('photo')
            );

            return ResponseHelper::success(
                $result,
                'Profile photo updated successfully'
            );
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }

    /**
     * Delete profile photo
     * 
     * @group Profile
     * @authenticated
     */
    public function deletePhoto(Request $request): JsonResponse
    {
        try {
            $this->profileService->deleteProfilePhoto($request->user());

            return ResponseHelper::deleted('Profile photo deleted successfully');
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage());
        }
    }
}