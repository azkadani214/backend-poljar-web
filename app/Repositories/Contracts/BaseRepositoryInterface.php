<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

interface BaseRepositoryInterface
{
    /**
     * Get all records
     */
    public function all(array $columns = ['*'], array $relations = []): Collection;

    /**
     * Get paginated records
     */
    public function paginate(
        int $perPage = 15,
        array $columns = ['*'],
        array $relations = [],
        array $filters = []
    ): LengthAwarePaginator;

    /**
     * Find record by ID
     */
    public function find(string $id, array $columns = ['*'], array $relations = []): ?Model;

    /**
     * Find record by ID or fail
     */
    public function findOrFail(string $id, array $columns = ['*'], array $relations = []): Model;

    /**
     * Find by specific column
     */
    public function findBy(string $column, $value, array $columns = ['*'], array $relations = []): ?Model;

    /**
     * Find where conditions
     */
    public function findWhere(array $conditions, array $columns = ['*'], array $relations = []): Collection;

    /**
     * Find where in
     */
    public function findWhereIn(string $column, array $values, array $columns = ['*']): Collection;

    /**
     * Create new record
     */
    public function create(array $data): Model;

    /**
     * Update record
     */
    public function update(string $id, array $data): Model;

    /**
     * Delete record
     */
    public function delete(string $id): bool;

    /**
     * Force delete record
     */
    public function forceDelete(string $id): bool;

    /**
     * Restore soft deleted record
     */
    public function restore(string $id): bool;

    /**
     * Check if record exists
     */
    public function exists(string $id): bool;

    /**
     * Count records
     */
    public function count(array $conditions = []): int;

    /**
     * Get first record
     */
    public function first(array $columns = ['*'], array $relations = []): ?Model;

    /**
     * Create or update record
     */
    public function updateOrCreate(array $attributes, array $values = []): Model;

    /**
     * Get records with trashed
     */
    public function withTrashed(): self;

    /**
     * Get only trashed records
     */
    public function onlyTrashed(): self;
}