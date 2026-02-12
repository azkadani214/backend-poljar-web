<?php

namespace App\Repositories\Eloquent;

use App\Models\Division;
use App\Repositories\Contracts\DivisionRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class DivisionRepository extends BaseRepository implements DivisionRepositoryInterface
{
    public function __construct(Division $model)
    {
        parent::__construct($model);
    }

    /**
     * Get divisions with positions count
     */
    public function getDivisionsWithPositionsCount(): Collection
    {
        return $this->model
            ->withCount('positions')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get divisions with members count
     */
    public function getDivisionsWithMembersCount(): Collection
    {
        return $this->model
            ->withCount(['users', 'memberships'])
            ->orderBy('name')
            ->get();
    }

    /**
     * Get divisions with relationships
     */
    public function getDivisionsWithRelations(): Collection
    {
        return $this->model
            ->with(['positions', 'memberships.user'])
            ->withCount(['positions', 'users'])
            ->orderBy('name')
            ->get();
    }

    /**
     * Find division by name
     */
    public function findByName(string $name): ?Division
    {
        return $this->findBy('name', $name, ['*'], ['positions', 'users']);
    }

    /**
     * Get divisions with active members
     */
    public function getDivisionsWithActiveMembers(): Collection
    {
        return $this->model
            ->with(['memberships' => function ($query) {
                $query->where('is_active', true)
                    ->with(['user', 'position']);
            }])
            ->whereHas('memberships', function ($query) {
                $query->where('is_active', true);
            })
            ->withCount(['memberships' => function ($query) {
                $query->where('is_active', true);
            }])
            ->orderBy('name')
            ->get();
    }

    /**
     * Get division statistics
     */
    public function getStatistics(string $divisionId): array
    {
        $division = $this->model
            ->withCount([
                'positions',
                'users',
                'memberships',
                'memberships as active_memberships_count' => function ($query) {
                    $query->where('is_active', true);
                }
            ])
            ->findOrFail($divisionId);

        return [
            'division_id' => $division->id,
            'division_name' => $division->name,
            'total_positions' => $division->positions_count,
            'total_members' => $division->users_count,
            'total_memberships' => $division->memberships_count,
            'active_memberships' => $division->active_memberships_count,
        ];
    }
}