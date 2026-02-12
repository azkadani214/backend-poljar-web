<?php

namespace App\Repositories\Contracts;

use App\Models\Membership;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface MembershipRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get memberships by user
     */
    public function getByUser(string $userId): Collection;

    /**
     * Get memberships by division
     */
    public function getByDivision(string $divisionId): Collection;

    /**
     * Get memberships by position
     */
    public function getByPosition(string $positionId): Collection;

    /**
     * Get active memberships
     */
    public function getActiveMemberships(int $perPage = 15): LengthAwarePaginator;

    /**
     * Get inactive memberships
     */
    public function getInactiveMemberships(int $perPage = 15): LengthAwarePaginator;

    /**
     * Find membership by user and division
     */
    public function findByUserAndDivision(string $userId, string $divisionId): ?Membership;

    /**
     * Activate membership
     */
    public function activate(string $id): bool;

    /**
     * Deactivate membership
     */
    public function deactivate(string $id): bool;

    /**
     * Get memberships by period
     */
    public function getByPeriod(string $period): Collection;

    /**
     * Get core team memberships
     */
    public function getCoreTeam(): Collection;

    /**
     * Get staff memberships
     */
    public function getStaff(): Collection;
}