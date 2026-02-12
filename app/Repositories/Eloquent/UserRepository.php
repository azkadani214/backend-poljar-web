<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    /**
     * Find user by email
     */
    public function findByEmail(string $email): ?User
    {
        return $this->findBy('email', $email, ['*'], ['memberships.division', 'memberships.position']);
    }

    /**
     * Get active users
     */
    public function getActiveUsers(int $perPage = 15): LengthAwarePaginator
    {
        $this->query = $this->query->where('status', 'active')
            ->with(['divisions', 'memberships'])
            ->orderBy('name');

        return $this->paginate($perPage);
    }

    /**
     * Get inactive users
     */
    public function getInactiveUsers(int $perPage = 15): LengthAwarePaginator
    {
        $this->query = $this->query->where('status', 'inactive')
            ->with(['divisions', 'memberships'])
            ->orderBy('name');

        return $this->paginate($perPage);
    }

    /**
     * Get users by division
     */
    public function getUsersByDivision(string $divisionId): Collection
    {
        return $this->model
            ->whereHas('memberships', function ($query) use ($divisionId) {
                $query->where('division_id', $divisionId)
                    ->where('is_active', true);
            })
            ->with(['memberships' => function ($query) use ($divisionId) {
                $query->where('division_id', $divisionId);
            }])
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get users with memberships
     */
    public function getUsersWithMemberships(int $perPage = 15): LengthAwarePaginator
    {
        $this->query = $this->query
            ->with(['memberships.division', 'memberships.position', 'roles'])
            ->withCount('memberships')
            ->orderBy('name');

        return $this->paginate($perPage);
    }

    /**
     * Search users
     */
    public function search(string $keyword, int $perPage = 15): LengthAwarePaginator
    {
        $this->query = $this->query
            ->where(function ($query) use ($keyword) {
                $query->where('name', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%");
            })
            ->with(['divisions', 'memberships', 'roles'])
            ->orderBy('name');

        return $this->paginate($perPage);
    }

    /**
     * Activate user
     */
    public function activate(string $id): bool
    {
        return $this->update($id, ['status' => 'active']);
    }

    /**
     * Deactivate user
     */
    public function deactivate(string $id): bool
    {
        return $this->update($id, ['status' => 'inactive']);
    }

    /**
     * Update user password
     */
    public function updatePassword(string $id, string $password): bool
    {
        return $this->update($id, [
            'password' => Hash::make($password)
        ]);
    }

    /**
     * Verify user email
     */
    public function verifyEmail(string $id): bool
    {
        return $this->update($id, [
            'email_verified_at' => now()
        ]);
    }

    /**
     * Get users count by status
     */
    public function countByStatus(string $status): int
    {
        return $this->count(['status' => $status]);
    }
}