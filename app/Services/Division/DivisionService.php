<?php

namespace App\Services\Division;

use App\Models\Division;
use App\Repositories\Contracts\DivisionRepositoryInterface;
use App\Repositories\Contracts\PositionRepositoryInterface;
use App\Repositories\Contracts\MembershipRepositoryInterface;
use App\Exceptions\Api\NotFoundException;
use App\Exceptions\Api\ValidationException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class DivisionService
{
    public function __construct(
        private DivisionRepositoryInterface $divisionRepository,
        private PositionRepositoryInterface $positionRepository,
        private MembershipRepositoryInterface $membershipRepository
    ) {}

    /**
     * Get all divisions
     */
    public function getAllDivisions(): Collection
    {
        return $this->divisionRepository->getDivisionsWithRelations();
    }

    /**
     * Get divisions with statistics
     */
    public function getDivisionsWithStatistics(): Collection
    {
        return $this->divisionRepository->getDivisionsWithMembersCount();
    }

    /**
     * Get division by ID
     */
    public function getDivisionById(string $id): Division
    {
        $division = $this->divisionRepository->find(
            $id,
            ['*'],
            ['positions', 'memberships.user', 'memberships.position']
        );

        if (!$division) {
            throw new NotFoundException('Division not found');
        }

        return $division;
    }

    /**
     * Create division
     */
    public function createDivision(array $data): Division
    {
        // Check if name already exists
        $existing = $this->divisionRepository->findByName($data['name']);
        if ($existing) {
            throw new ValidationException('Division name already exists');
        }

        return $this->divisionRepository->create($data);
    }

    /**
     * Update division
     */
    public function updateDivision(string $id, array $data): Division
    {
        $division = $this->getDivisionById($id);

        // Check if new name already exists (excluding current division)
        if (isset($data['name']) && $data['name'] !== $division->name) {
            $existing = $this->divisionRepository->findByName($data['name']);
            if ($existing) {
                throw new ValidationException('Division name already exists');
            }
        }

        $this->divisionRepository->update($id, $data);

        return $division->fresh(['positions', 'memberships']);
    }

    /**
     * Delete division
     */
    public function deleteDivision(string $id): bool
    {
        DB::beginTransaction();

        try {
            $division = $this->getDivisionById($id);

            // Check if division has positions
            $positionsCount = $this->positionRepository->getByDivision($id)->count();
            if ($positionsCount > 0) {
                throw new ValidationException(
                    'Cannot delete division with existing positions. Please delete or reassign positions first.'
                );
            }

            // Check if division has active memberships
            $membershipsCount = $this->membershipRepository->getByDivision($id)->count();
            if ($membershipsCount > 0) {
                throw new ValidationException(
                    'Cannot delete division with existing memberships. Please remove or reassign members first.'
                );
            }

            $result = $this->divisionRepository->delete($id);

            DB::commit();

            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get division statistics
     */
    public function getDivisionStatistics(string $id): array
    {
        $this->getDivisionById($id); // Check if exists

        return $this->divisionRepository->getStatistics($id);
    }

    /**
     * Get division members
     */
    public function getDivisionMembers(string $id): Collection
    {
        $this->getDivisionById($id); // Check if exists

        return $this->membershipRepository->getByDivision($id);
    }

    /**
     * Get divisions with active members
     */
    public function getDivisionsWithActiveMembers(): Collection
    {
        return $this->divisionRepository->getDivisionsWithActiveMembers();
    }
}
