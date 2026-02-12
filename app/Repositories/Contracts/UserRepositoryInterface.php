<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Find user by email
     */
    public function findByEmail(string $email): ?User;

    /**
     * Get active users
     */
    public function getActiveUsers(int $perPage = 15): LengthAwarePaginator;

    /**
     * Get inactive users
     */
    public function getInactiveUsers(int $perPage = 15): LengthAwarePaginator;

    /**
     * Get users by division
     */
    public function getUsersByDivision(string $divisionId): Collection;

    /**
     * Get users with memberships
     */
    public function getUsersWithMemberships(int $perPage = 15): LengthAwarePaginator;

    /**
     * Search users
     */
    public function search(string $keyword, int $perPage = 15): LengthAwarePaginator;

    /**
     * Activate user
     */
    public function activate(string $id): bool;

    /**
     * Deactivate user
     */
    public function deactivate(string $id): bool;

    /**
     * Update user password
     */
    public function updatePassword(string $id, string $password): bool;

    /**
     * Verify user email
     */
    public function verifyEmail(string $id): bool;

    /**
     * Get users count by status
     */
    public function countByStatus(string $status): int;
}