<?php
// ============================================================================
// FILE 50: app/Services/User/UserService.php
// ============================================================================

namespace App\Services\User;

use App\Models\User;
use App\Models\ActivityLog;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Services\Upload\ImageUploadService;
use App\Exceptions\Api\NotFoundException;
use App\Exceptions\Api\ValidationException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private ImageUploadService $imageUploadService
    ) {}

    /**
     * Get all users with filters
     */
    public function getAllUsers(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        if (isset($filters['search'])) {
            return $this->userRepository->search($filters['search'], $perPage);
        }

        if (isset($filters['status'])) {
            if ($filters['status'] === 'active') {
                return $this->userRepository->getActiveUsers($perPage);
            }
            if ($filters['status'] === 'inactive') {
                return $this->userRepository->getInactiveUsers($perPage);
            }
        }

        if (isset($filters['division_id'])) {
            $users = $this->userRepository->getUsersByDivision($filters['division_id']);
            return new LengthAwarePaginator(
                $users->forPage($perPage, 1),
                $users->count(),
                $perPage
            );
        }

        return $this->userRepository->getUsersWithMemberships($perPage);
    }

    /**
     * Get user by ID
     */
    public function getUserById(string $id): User
    {
        $user = $this->userRepository->find(
            $id,
            ['*'],
            ['divisions', 'memberships.division', 'memberships.position', 'roles']
        );

        if (!$user) {
            throw new NotFoundException('User not found');
        }

        return $user;
    }

    /**
     * Create new user
     */
    public function createUser(array $data): User
    {
        DB::beginTransaction();

        try {
            // Check if email exists
            $existingUser = $this->userRepository->findByEmail($data['email']);
            if ($existingUser) {
                throw new ValidationException('Email already exists');
            }

            // Handle photo upload
            if (isset($data['photo'])) {
                $uploadResult = $this->imageUploadService->uploadAvatar(
                    $data['photo'],
                    'user-photos'
                );
                $data['photo'] = $uploadResult['path'];
            }

            // Hash password
            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }

            // Set default status
            $data['status'] = $data['status'] ?? 'active';

            // Create user
            $user = $this->userRepository->create($data);

            // Assign roles if provided
            if (isset($data['roles']) && !empty($data['roles'])) {
                $user->syncRoles($data['roles']);
            }

            DB::commit();

            return $user->load(['divisions', 'memberships']);
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Delete uploaded photo if exists
            if (isset($data['photo'])) {
                $this->imageUploadService->delete($data['photo']);
            }

            throw $e;
        }
    }

    /**
     * Update user
     */
    public function updateUser(string $id, array $data): User
    {
        DB::beginTransaction();

        try {
            $user = $this->getUserById($id);

            // Check email uniqueness if changed
            if (isset($data['email']) && $data['email'] !== $user->email) {
                $existingUser = $this->userRepository->findByEmail($data['email']);
                if ($existingUser) {
                    throw new ValidationException('Email already exists');
                }
            }

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

            // Don't update password here (use separate method)
            unset($data['password']);

            // Update user
            $this->userRepository->update($id, $data);

            // Update roles if provided
            if (isset($data['roles'])) {
                $user->syncRoles($data['roles']);
            }

            DB::commit();

            return $user->fresh(['divisions', 'memberships']);
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Delete uploaded photo if exists and error occurred
            if (isset($data['photo']) && is_string($data['photo'])) {
                $this->imageUploadService->delete($data['photo']);
            }

            throw $e;
        }
    }

    /**
     * Delete user
     */
    public function deleteUser(string $id): bool
    {
        DB::beginTransaction();

        try {
            $user = $this->getUserById($id);

            // Delete user photo
            if ($user->photo) {
                $this->imageUploadService->delete($user->photo);
            }

            // Delete user
            $result = $this->userRepository->delete($id);

            DB::commit();

            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Activate user
     */
    public function activateUser(string $id): User
    {
        $user = $this->getUserById($id);
        
        if ($user->status === 'active') {
            throw new ValidationException('User is already active');
        }

        $this->userRepository->activate($id);

        return $user->fresh();
    }

    /**
     * Deactivate user
     */
    public function deactivateUser(string $id): User
    {
        $user = $this->getUserById($id);
        
        if ($user->status === 'inactive') {
            throw new ValidationException('User is already inactive');
        }

        $this->userRepository->deactivate($id);

        return $user->fresh();
    }

    /**
     * Search users
     */
    public function searchUsers(string $keyword, int $perPage = 15): LengthAwarePaginator
    {
        return $this->userRepository->search($keyword, $perPage);
    }

    /**
     * Update user password
     */
    public function updatePassword(string $id, string $password): bool
    {
        $this->getUserById($id); // Check if user exists

        return $this->userRepository->updatePassword($id, $password);
    }

    /**
     * Get users by division
     */
    public function getUsersByDivision(string $divisionId): Collection
    {
        return $this->userRepository->getUsersByDivision($divisionId);
    }

    /**
     * Get user statistics
     */
    public function getUserStatistics(): array
    {
        return [
            'total_users' => $this->userRepository->count(),
            'active_users' => $this->userRepository->countByStatus('active'),
            'inactive_users' => $this->userRepository->countByStatus('inactive'),
        ];
    }

    /**
     * Bulk activate users
     */
    public function bulkActivate(array $userIds): int
    {
        $count = 0;
        
        foreach ($userIds as $userId) {
            try {
                $this->activateUser($userId);
                $count++;
            } catch (\Exception $e) {
                // Continue with other users
                continue;
            }
        }

        return $count;
    }

    /**
     * Bulk deactivate users
     */
    public function bulkDeactivate(array $userIds): int
    {
        $count = 0;
        
        foreach ($userIds as $userId) {
            try {
                $this->deactivateUser($userId);
                $count++;
            } catch (\Exception $e) {
                // Continue with other users
                continue;
            }
        }

        return $count;
    }

    /**
     * Get all activity logs
     */
    public function getAllActivities(int $perPage = 15): LengthAwarePaginator
    {
        return ActivityLog::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
}