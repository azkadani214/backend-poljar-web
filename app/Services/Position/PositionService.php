<?php

namespace App\Services\Position;

use App\Models\Position;
use App\Repositories\Contracts\PositionRepositoryInterface;
use App\Repositories\Contracts\DivisionRepositoryInterface;
use App\Repositories\Contracts\MembershipRepositoryInterface;
use App\Exceptions\Api\NotFoundException;
use App\Exceptions\Api\ValidationException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class PositionService
{
    public function __construct(
        private PositionRepositoryInterface $positionRepository,
        private DivisionRepositoryInterface $divisionRepository,
        private MembershipRepositoryInterface $membershipRepository
    ) {}

    /**
     * Get all positions
     */
    public function getAllPositions(): Collection
    {
        return $this->positionRepository->getPositionsWithMembersCount();
    }

    /**
     * Get positions by division
     */
    public function getPositionsByDivision(string $divisionId): Collection
    {
        // Check if division exists
        $division = $this->divisionRepository->findOrFail($divisionId);

        return $this->positionRepository->getByDivision($divisionId);
    }

    /**
     * Get position by ID
     */
    public function getPositionById(string $id): Position
    {
        $position = $this->positionRepository->find(
            $id,
            ['*'],
            ['division', 'memberships.user']
        );

        if (!$position) {
            throw new NotFoundException('Position not found');
        }

        return $position;
    }

    /**
     * Create position
     */
    public function createPosition(array $data): Position
    {
        // Check if division exists
        $this->divisionRepository->findOrFail($data['division_id']);

        // Check if position name already exists in this division
        $existing = $this->positionRepository->findByNameAndDivision(
            $data['name'],
            $data['division_id']
        );

        if ($existing) {
            throw new ValidationException('Position name already exists in this division');
        }

        return $this->positionRepository->create($data);
    }

    /**
     * Update position
     */
    public function updatePosition(string $id, array $data): Position
    {
        $position = $this->getPositionById($id);

        // Check if division exists (if changing division)
        if (isset($data['division_id'])) {
            $this->divisionRepository->findOrFail($data['division_id']);
        }

        // Check if new name already exists in division
        if (isset($data['name']) && $data['name'] !== $position->name) {
            $divisionId = $data['division_id'] ?? $position->division_id;
            $existing = $this->positionRepository->findByNameAndDivision($data['name'], $divisionId);
            
            if ($existing) {
                throw new ValidationException('Position name already exists in this division');
            }
        }

        $this->positionRepository->update($id, $data);

        return $position->fresh(['division', 'memberships']);
    }

    /**
     * Delete position
     */
    public function deletePosition(string $id): bool
    {
        DB::beginTransaction();

        try {
            $position = $this->getPositionById($id);

            // Check if position has memberships
            $membershipsCount = $this->membershipRepository->getByPosition($id)->count();
            if ($membershipsCount > 0) {
                throw new ValidationException(
                    'Cannot delete position with existing memberships. Please remove or reassign members first.'
                );
            }

            $result = $this->positionRepository->delete($id);

            DB::commit();

            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get core team positions
     */
    public function getCoreTeamPositions(): Collection
    {
        return $this->positionRepository->getCoreTeamPositions();
    }

    /**
     * Get staff positions
     */
    public function getStaffPositions(): Collection
    {
        return $this->positionRepository->getStaffPositions();
    }

    /**
     * Get positions by level
     */
    public function getPositionsByLevel(int $level): Collection
    {
        return $this->positionRepository->getByLevel($level);
    }
}
