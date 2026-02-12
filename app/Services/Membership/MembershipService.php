<?php

namespace App\Services\Membership;

use App\Models\Membership;
use App\Repositories\Contracts\MembershipRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\DivisionRepositoryInterface;
use App\Repositories\Contracts\PositionRepositoryInterface;
use App\Exceptions\Api\NotFoundException;
use App\Exceptions\Api\ValidationException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class MembershipService
{
    public function __construct(
        private MembershipRepositoryInterface $membershipRepository,
        private UserRepositoryInterface $userRepository,
        private DivisionRepositoryInterface $divisionRepository,
        private PositionRepositoryInterface $positionRepository
    ) {}

    /**
     * Get all memberships
     */
    public function getAllMemberships(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        if (isset($filters['is_active'])) {
            return $filters['is_active'] 
                ? $this->membershipRepository->getActiveMemberships($perPage)
                : $this->membershipRepository->getInactiveMemberships($perPage);
        }

        return $this->membershipRepository->paginate($perPage, ['*'], ['user', 'division', 'position']);
    }

    /**
     * Get membership by ID
     */
    public function getMembershipById(string $id): Membership
    {
        $membership = $this->membershipRepository->find(
            $id,
            ['*'],
            ['user', 'division', 'position']
        );

        if (!$membership) {
            throw new NotFoundException('Membership not found');
        }

        return $membership;
    }

    /**
     * Create membership
     */
    public function createMembership(array $data): Membership
    {
        // Validate user exists
        $this->userRepository->findOrFail($data['user_id']);

        // Validate division exists
        $this->divisionRepository->findOrFail($data['division_id']);

        // Validate position exists
        $this->positionRepository->findOrFail($data['position_id']);

        // Check if user already has membership in this division
        $existing = $this->membershipRepository->findByUserAndDivision(
            $data['user_id'],
            $data['division_id']
        );

        if ($existing) {
            throw new ValidationException('User already has a membership in this division');
        }

        // Set default values
        $data['is_active'] = $data['is_active'] ?? true;
        $data['period'] = $data['period'] ?? date('Y');

        return $this->membershipRepository->create($data);
    }

    /**
     * Update membership
     */
    public function updateMembership(string $id, array $data): Membership
    {
        $membership = $this->getMembershipById($id);

        // Validate division if changing
        if (isset($data['division_id']) && $data['division_id'] !== $membership->division_id) {
            $this->divisionRepository->findOrFail($data['division_id']);

            // Check if user already has membership in new division
            $existing = $this->membershipRepository->findByUserAndDivision(
                $membership->user_id,
                $data['division_id']
            );

            if ($existing) {
                throw new ValidationException('User already has a membership in this division');
            }
        }

        // Validate position if changing
        if (isset($data['position_id'])) {
            $this->positionRepository->findOrFail($data['position_id']);
        }

        $this->membershipRepository->update($id, $data);

        return $membership->fresh(['user', 'division', 'position']);
    }

    /**
     * Delete membership
     */
    public function deleteMembership(string $id): bool
    {
        $this->getMembershipById($id); // Check if exists

        return $this->membershipRepository->delete($id);
    }

    /**
     * Activate membership
     */
    public function activateMembership(string $id): Membership
    {
        $membership = $this->getMembershipById($id);

        if ($membership->is_active) {
            throw new ValidationException('Membership is already active');
        }

        $this->membershipRepository->activate($id);

        return $membership->fresh();
    }

    /**
     * Deactivate membership
     */
    public function deactivateMembership(string $id): Membership
    {
        $membership = $this->getMembershipById($id);

        if (!$membership->is_active) {
            throw new ValidationException('Membership is already inactive');
        }

        $this->membershipRepository->deactivate($id);

        return $membership->fresh();
    }

    /**
     * Get memberships by user
     */
    public function getMembershipsByUser(string $userId): Collection
    {
        $this->userRepository->findOrFail($userId);

        return $this->membershipRepository->getByUser($userId);
    }

    /**
     * Get memberships by division
     */
    public function getMembershipsByDivision(string $divisionId): Collection
    {
        $this->divisionRepository->findOrFail($divisionId);

        return $this->membershipRepository->getByDivision($divisionId);
    }

    /**
     * Get memberships by position
     */
    public function getMembershipsByPosition(string $positionId): Collection
    {
        $this->positionRepository->findOrFail($positionId);

        return $this->membershipRepository->getByPosition($positionId);
    }

    /**
     * Get core team
     */
    public function getCoreTeam(): Collection
    {
        return $this->membershipRepository->getCoreTeam();
    }

    /**
     * Get staff
     */
    public function getStaff(): Collection
    {
        return $this->membershipRepository->getStaff();
    }

    /**
     * Get memberships by period
     */
    public function getMembershipsByPeriod(string $period): Collection
    {
        return $this->membershipRepository->getByPeriod($period);
    }
}