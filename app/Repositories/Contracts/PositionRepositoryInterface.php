<?php

namespace App\Repositories\Contracts;

use App\Models\Position;
use Illuminate\Database\Eloquent\Collection;

interface PositionRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get positions by division
     */
    public function getByDivision(string $divisionId): Collection;

    /**
     * Get positions by level
     */
    public function getByLevel(int $level): Collection;

    /**
     * Get positions with members count
     */
    public function getPositionsWithMembersCount(): Collection;

    /**
     * Find position by name and division
     */
    public function findByNameAndDivision(string $name, string $divisionId): ?Position;

    /**
     * Get core team positions (level >= 5)
     */
    public function getCoreTeamPositions(): Collection;

    /**
     * Get staff positions (level < 5)
     */
    public function getStaffPositions(): Collection;
}