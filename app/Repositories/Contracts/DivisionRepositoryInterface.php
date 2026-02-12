<?php

namespace App\Repositories\Contracts;

use App\Models\Division;
use Illuminate\Database\Eloquent\Collection;

interface DivisionRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get divisions with positions count
     */
    public function getDivisionsWithPositionsCount(): Collection;

    /**
     * Get divisions with members count
     */
    public function getDivisionsWithMembersCount(): Collection;

    /**
     * Get divisions with relationships
     */
    public function getDivisionsWithRelations(): Collection;

    /**
     * Find division by name
     */
    public function findByName(string $name): ?Division;

    /**
     * Get divisions with active members
     */
    public function getDivisionsWithActiveMembers(): Collection;

    /**
     * Get division statistics
     */
    public function getStatistics(string $divisionId): array;
}