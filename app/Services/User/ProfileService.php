<?php

namespace App\Services\User;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Services\Upload\ImageUploadService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ProfileService
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private ImageUploadService $imageUploadService
    ) {}

    /**
     * Get user profile
     */
    public function getProfile(User $user): User
    {
        return $user->load(['divisions', 'memberships.division', 'memberships.position', 'roles']);
    }

    /**
     * Update profile
     */
    public function updateProfile(User $user, array $data): User
    {
        DB::beginTransaction();

        try {
            // Handle photo upload
            if (isset($data['photo'])) {
                // Delete old photo
                if ($user->photo) {
                    $this->imageUploadService->delete($user->photo);
                }

                // Upload new photo
                $uploadResult = $this->imageUploadService->uploadAvatar(
                    $data['photo'],
                    'user-photos'
                );
                $data['photo'] = $uploadResult['path'];
            }

            // Don't allow changing email and password through this method
            unset($data['email'], $data['password'], $data['status']);

            // Update user
            $this->userRepository->update($user->id, $data);

            DB::commit();

            return $user->fresh(['divisions', 'memberships']);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Change password
     */
    public function changePassword(User $user, string $currentPassword, string $newPassword): bool
    {
        if (!Hash::check($currentPassword, $user->password)) {
            throw new \Exception('Current password is incorrect');
        }

        return $this->userRepository->updatePassword($user->id, $newPassword);
    }

    /**
     * Update profile photo
     */
    public function updateProfilePhoto(User $user, $photo): array
    {
        DB::beginTransaction();

        try {
            // Delete old photo
            if ($user->photo) {
                $this->imageUploadService->delete($user->photo);
            }

            // Upload new photo
            $uploadResult = $this->imageUploadService->uploadAvatar(
                $photo,
                'user-photos'
            );

            // Update user photo
            $this->userRepository->update($user->id, [
                'photo' => $uploadResult['path']
            ]);

            DB::commit();

            return $uploadResult;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete profile photo
     */
    public function deleteProfilePhoto(User $user): bool
    {
        if (!$user->photo) {
            throw new \Exception('No profile photo to delete');
        }

        DB::beginTransaction();

        try {
            // Delete photo file
            $this->imageUploadService->delete($user->photo);

            // Update user
            $this->userRepository->update($user->id, ['photo' => null]);

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}