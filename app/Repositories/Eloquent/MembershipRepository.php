<?php

namespace App\Repositories\Eloquent;

use App\Models\Membership;
use App\Repositories\Contracts\MembershipRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class MembershipRepository extends BaseRepository implements MembershipRepositoryInterface
{
    public function __construct(Membership $model)
    {
        parent::__construct($model);
    }

    /**
     * Get memberships by user
     */
    public function getByUser(string $userId): Collection
    {
        return $this->model
            ->where('user_id', $userId)
            ->with(['division', 'position'])
            ->orderBy('is_active', 'desc')
            ->get();
    }

    /**
     * Get memberships by division
     */
    public function getByDivision(string $divisionId): Collection
    {
        return $this->model
            ->where('division_id', $divisionId)
            ->with(['user', 'position'])
            ->orderBy('is_active', 'desc')
            ->get();
    }

    /**
     * Get memberships by position
     */
    public function getByPosition(string $positionId): Collection
    {
        return $this->model
            ->where('position_id', $positionId)
            ->with(['user', 'division'])
            ->orderBy('is_active', 'desc')
            ->get();
    }

    /**
     * Get active memberships
     */
    public function getActiveMemberships(int $perPage = 15): LengthAwarePaginator
    {
        $this->query = $this->query
            ->where('is_active', true)
            ->with(['user', 'division', 'position']);

        return $this->paginate($perPage);
    }

    /**
     * Get inactive memberships
     */
    public function getInactiveMemberships(int $perPage = 15): LengthAwarePaginator
    {
        $this->query = $this->query
            ->where('is_active', false)
            ->with(['user', 'division', 'position']);

        return $this->paginate($perPage);
    }

    /**
     * Find membership by user and division
     */
    public function findByUserAndDivision(string $userId, string $divisionId): ?Membership
    {
        return $this->model
            ->where('user_id', $userId)
            ->where('division_id', $divisionId)
            ->with(['division', 'position'])
            ->first();
    }

    /**
     * Activate membership
     */
    public function activate(string $id): bool
    {
        return $this->update($id, ['is_active' => true]);
    }

    /**
     * Deactivate membership
     */
    public function deactivate(string $id): bool
    {
        return $this->update($id, ['is_active' => false]);
    }

    /**
     * Get memberships by period
     */
    public function getByPeriod(string $period): Collection
    {
        return $this->model
            ->where('period', $period)
            ->with(['user', 'division', 'position'])
            ->orderBy('is_active', 'desc')
            ->get();
    }

    /**
     * Get core team memberships
     */
    public function getCoreTeam(): Collection
    {
        return $this->model
            ->whereHas('position', function ($query) {
                $query->where('level', '<=', 3);
            })
            ->whereHas('user', function ($query) {
                $query->where('status', 'active');
            })
            ->where('is_active', true)
            ->with(['user', 'division', 'position'])
            ->join('positions', 'memberships.position_id', '=', 'positions.id')
            ->orderBy('positions.level', 'asc')
            ->select('memberships.*')
            ->get();
    }

    /**
     * Get staff memberships
     */
    public function getStaff(): Collection
    {
        return $this->model
            ->whereHas('position', function ($query) {
                $query->where('level', '>', 3);
            })
            ->whereHas('user', function ($query) {
                $query->where('status', 'active');
            })
            ->where('is_active', true)
            ->with(['user', 'division', 'position'])
            ->join('positions', 'memberships.position_id', '=', 'positions.id')
            ->orderBy('positions.level', 'asc')
            ->select('memberships.*')
            ->get();
    }
}