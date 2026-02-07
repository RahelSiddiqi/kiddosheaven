<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class BaseRepository implements RepositoryInterface
{
    protected Model $model;

    protected array $relations = [];

    protected string $orderByColumn = 'created_at';

    protected string $orderByDirection = 'desc';

    protected array $whereConditions = [];

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Get all records
     */
    public function all(array $columns = ['*']): Collection
    {
        return $this->model->with($this->relations)->orderBy($this->orderByColumn, $this->orderByDirection)->get($columns);
    }

    /**
     * Find a record by ID
     */
    public function find(int $id): ?Model
    {
        return $this->model->with($this->relations)->find($id);
    }

    /**
     * Find a record by specific field
     */
    public function findBy(string $field, $value): ?Model
    {
        return $this->model->where($field, $value)->first();
    }

    /**
     * Find records by specific field
     */
    public function findAllBy(string $field, $value): Collection
    {
        return $this->model->where($field, $value)->orderBy($this->orderByColumn, $this->orderByDirection)->get();
    }

    /**
     * Create a new record
     */
    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    /**
     * Update a record
     */
    public function update(int $id, array $data): Model
    {
        $record = $this->find($id);

        if (!$record) {
            throw new \Exception("Record not found with ID: {$id}");
        }

        $record->update($data);

        return $record->fresh();
    }

    /**
     * Delete a record
     */
    public function delete(int $id): bool
    {
        $record = $this->find($id);

        if (!$record) {
            return false;
        }

        return $record->delete();
    }

    /**
     * Paginate records
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with($this->relations)
            ->orderBy($this->orderByColumn, $this->orderByDirection)
            ->paginate($perPage);
    }

    /**
     * Get records with relationships
     */
    public function with(array $relations): self
    {
        $this->relations = $relations;

        return $this;
    }

    /**
     * Order records
     */
    public function orderBy(string $column, string $direction = 'asc'): self
    {
        $this->orderByColumn = $column;
        $this->orderByDirection = $direction;

        return $this;
    }

    /**
     * Apply where conditions
     */
    public function where(array $conditions): self
    {
        foreach ($conditions as $field => $value) {
            $this->model = $this->model->where($field, $value);
        }

        return $this;
    }

    /**
     * Get the model instance
     */
    public function getModel(): Model
    {
        return $this->model;
    }

    /**
     * Count records
     */
    public function count(): int
    {
        return $this->model->count();
    }

    /**
     * Check if record exists
     */
    public function exists(int $id): bool
    {
        return $this->model->where('id', $id)->exists();
    }
}
