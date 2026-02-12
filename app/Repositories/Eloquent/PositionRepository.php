<?php

namespace App\Repositories\Eloquent;

use App\Models\Position;
use App\Repositories\Contracts\PositionRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class PositionRepository extends BaseRepository implements PositionRepositoryInterface
{
    public function __construct(Position $model)
    {
        parent::__construct($model);
    }

    /**
     * Get positions by division
     */
    public function getByDivision(string $divisionId): Collection
    {
        return $this->model
            ->where('division_id', $divisionId)
            ->with('division')
            ->orderBy('level', 'desc')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get positions by level
     */
    public function getByLevel(int $level): Collection
    {
        return $this->model
            ->where('level', $level)
            ->with('division')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get positions with members count
     */
    public function getPositionsWithMembersCount(): Collection
    {
        return $this->model
            ->withCount('memberships')
            ->with('division')
            ->orderBy('level', 'desc')
            ->orderBy('name')
            ->get();
    }

    /**
     * Find position by name and division
     */
    public function findByNameAndDivision(string $name, string $divisionId): ?Position
    {
        return $this->model
            ->where('name', $name)
            ->where('division_id', $divisionId)
            ->with('division')
            ->first();
    }

    /**
     * Get core team positions (level >= 5)
     */
    public function getCoreTeamPositions(): Collection
    {
        return $this->model
            ->where('level', '>=', 5)
            ->with('division')
            ->withCount('memberships')
            ->orderBy('level', 'desc')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get staff positions (level < 5)
     */
    public function getStaffPositions(): Collection
    {
        return $this->model
            ->where('level', '<', 5)
            ->with('division')
            ->withCount('memberships')
            ->orderBy('level', 'desc')
            ->orderBy('name')
            ->get();
    }
}