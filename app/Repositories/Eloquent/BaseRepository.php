<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

abstract class BaseRepository implements BaseRepositoryInterface
{
    protected Model $model;
    protected Builder $query;
    protected bool $withTrashedFlag = false;
    protected bool $onlyTrashedFlag = false;

    public function __construct(Model $model)
    {
        $this->model = $model;
        $this->resetQuery();
    }

    /**
     * Reset query builder
     */
    protected function resetQuery(): void
    {
        $this->query = $this->model->newQuery();
        
        if ($this->withTrashedFlag) {
            $this->query->withTrashed();
            $this->withTrashedFlag = false;
        }
        
        if ($this->onlyTrashedFlag) {
            $this->query->onlyTrashed();
            $this->onlyTrashedFlag = false;
        }
    }

    /**
     * Get all records
     */
    public function all(array $columns = ['*'], array $relations = []): Collection
    {
        $query = $this->query;
        
        if (!empty($relations)) {
            $query = $query->with($relations);
        }
        
        $result = $query->get($columns);
        $this->resetQuery();
        
        return $result;
    }

    /**
     * Get paginated records
     */
    public function paginate(
        int $perPage = 15,
        array $columns = ['*'],
        array $relations = [],
        array $filters = []
    ): LengthAwarePaginator {
        $query = $this->query;
        
        if (!empty($relations)) {
            $query = $query->with($relations);
        }
        
        // Apply filters
        foreach ($filters as $key => $value) {
            if (is_array($value)) {
                $query = $query->whereIn($key, $value);
            } else {
                $query = $query->where($key, $value);
            }
        }
        
        $result = $query->paginate($perPage, $columns);
        $this->resetQuery();
        
        return $result;
    }

    /**
     * Find record by ID
     */
    public function find(string $id, array $columns = ['*'], array $relations = []): ?Model
    {
        $query = $this->query;
        
        if (!empty($relations)) {
            $query = $query->with($relations);
        }
        
        $result = $query->find($id, $columns);
        $this->resetQuery();
        
        return $result;
    }

    /**
     * Find record by ID or fail
     */
    public function findOrFail(string $id, array $columns = ['*'], array $relations = []): Model
    {
        $query = $this->query;
        
        if (!empty($relations)) {
            $query = $query->with($relations);
        }
        
        $result = $query->findOrFail($id, $columns);
        $this->resetQuery();
        
        return $result;
    }

    /**
     * Find by specific column
     */
    public function findBy(string $column, $value, array $columns = ['*'], array $relations = []): ?Model
    {
        $query = $this->query;
        
        if (!empty($relations)) {
            $query = $query->with($relations);
        }
        
        $result = $query->where($column, $value)->first($columns);
        $this->resetQuery();
        
        return $result;
    }

    /**
     * Find where conditions
     */
    public function findWhere(array $conditions, array $columns = ['*'], array $relations = []): Collection
    {
        $query = $this->query;
        
        if (!empty($relations)) {
            $query = $query->with($relations);
        }
        
        foreach ($conditions as $key => $value) {
            $query = $query->where($key, $value);
        }
        
        $result = $query->get($columns);
        $this->resetQuery();
        
        return $result;
    }

    /**
     * Find where in
     */
    public function findWhereIn(string $column, array $values, array $columns = ['*']): Collection
    {
        $result = $this->query->whereIn($column, $values)->get($columns);
        $this->resetQuery();
        
        return $result;
    }

    /**
     * Create new record
     */
    public function create(array $data): Model
    {
        $result = $this->model->create($data);
        $this->resetQuery();
        
        return $result;
    }

    /**
     * Update record
     */
    public function update(string $id, array $data): Model
    {
        $model = $this->findOrFail($id);
        $model->update($data);
        $this->resetQuery();
        
        return $model;
    }

    /**
     * Delete record
     */
    public function delete(string $id): bool
    {
        $model = $this->findOrFail($id);
        $result = $model->delete();
        $this->resetQuery();
        
        return $result;
    }

    /**
     * Force delete record
     */
    public function forceDelete(string $id): bool
    {
        $model = $this->model->withTrashed()->findOrFail($id);
        $result = $model->forceDelete();
        $this->resetQuery();
        
        return $result;
    }

    /**
     * Restore soft deleted record
     */
    public function restore(string $id): bool
    {
        $model = $this->model->onlyTrashed()->findOrFail($id);
        $result = $model->restore();
        $this->resetQuery();
        
        return $result;
    }

    /**
     * Check if record exists
     */
    public function exists(string $id): bool
    {
        $result = $this->query->where('id', $id)->exists();
        $this->resetQuery();
        
        return $result;
    }

    /**
     * Count records
     */
    public function count(array $conditions = []): int
    {
        $query = $this->query;
        
        foreach ($conditions as $key => $value) {
            $query = $query->where($key, $value);
        }
        
        $result = $query->count();
        $this->resetQuery();
        
        return $result;
    }

    /**
     * Get first record
     */
    public function first(array $columns = ['*'], array $relations = []): ?Model
    {
        $query = $this->query;
        
        if (!empty($relations)) {
            $query = $query->with($relations);
        }
        
        $result = $query->first($columns);
        $this->resetQuery();
        
        return $result;
    }

    /**
     * Create or update record
     */
    public function updateOrCreate(array $attributes, array $values = []): Model
    {
        $result = $this->model->updateOrCreate($attributes, $values);
        $this->resetQuery();
        
        return $result;
    }

    /**
     * Get records with trashed
     */
    public function withTrashed(): self
    {
        $this->withTrashedFlag = true;
        $this->resetQuery();
        
        return $this;
    }

    /**
     * Get only trashed records
     */
    public function onlyTrashed(): self
    {
        $this->onlyTrashedFlag = true;
        $this->resetQuery();
        
        return $this;
    }

    /**
     * Begin database transaction
     */
    protected function beginTransaction(): void
    {
        DB::beginTransaction();
    }

    /**
     * Commit database transaction
     */
    protected function commit(): void
    {
        DB::commit();
    }

    /**
     * Rollback database transaction
     */
    protected function rollback(): void
    {
        DB::rollBack();
    }
}