<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\RepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

abstract class BaseRepository implements RepositoryInterface
{
    /**
     * @var Model
     */
    protected Model $model;

    /**
     * BaseRepository constructor
     *
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Get all records
     *
     * @param array $columns
     * @return Collection
     */
    public function all(array $columns = ['*']): Collection
    {
        return $this->model->select($columns)->get();
    }

    /**
     * Get all records with relationships
     *
     * @param array $relations
     * @return Collection
     */
    public function allWith(array $relations): Collection
    {
        return $this->model->with($relations)->get();
    }

    /**
     * Find a record by ID
     *
     * @param int $id
     * @return Model|null
     */
    public function find(int $id): ?Model
    {
        return $this->model->find($id);
    }

    /**
     * Find a record by ID or fail
     *
     * @param int $id
     * @return Model
     */
    public function findOrFail(int $id): Model
    {
        return $this->model->findOrFail($id);
    }

    /**
     * Find a record by specific column
     *
     * @param string $column
     * @param mixed $value
     * @return Model|null
     */
    public function findBy(string $column, $value): ?Model
    {
        return $this->model->where($column, $value)->first();
    }

    /**
     * Find records by specific column
     *
     * @param string $column
     * @param mixed $value
     * @return Collection
     */
    public function findAllBy(string $column, $value): Collection
    {
        return $this->model->where($column, $value)->get();
    }

    /**
     * Create a new record
     *
     * @param array $data
     * @return Model
     */
    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    /**
     * Update a record
     *
     * @param int $id
     * @param array $data
     * @return Model
     */
    public function update(int $id, array $data): Model
    {
        $record = $this->findOrFail($id);
        $record->update($data);
        return $record->fresh();
    }

    /**
     * Delete a record
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $record = $this->findOrFail($id);
        return $record->delete();
    }

    /**
     * Get paginated records
     *
     * @param int $perPage
     * @param array $columns
     * @return LengthAwarePaginator
     */
    public function paginate(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator
    {
        return $this->model->select($columns)->paginate($perPage);
    }

    /**
     * Get records with where clause
     *
     * @param string $column
     * @param mixed $value
     * @param string $operator
     * @return Collection
     */
    public function where(string $column, $value, string $operator = '='): Collection
    {
        return $this->model->where($column, $operator, $value)->get();
    }

    /**
     * Get records with whereIn clause
     *
     * @param string $column
     * @param array $values
     * @return Collection
     */
    public function whereIn(string $column, array $values): Collection
    {
        return $this->model->whereIn($column, $values)->get();
    }

    /**
     * Count records
     *
     * @return int
     */
    public function count(): int
    {
        return $this->model->count();
    }

    /**
     * Check if record exists
     *
     * @param int $id
     * @return bool
     */
    public function exists(int $id): bool
    {
        return $this->model->where('id', $id)->exists();
    }

    /**
     * Begin database transaction
     *
     * @return void
     */
    public function beginTransaction(): void
    {
        DB::beginTransaction();
    }

    /**
     * Commit database transaction
     *
     * @return void
     */
    public function commit(): void
    {
        DB::commit();
    }

    /**
     * Rollback database transaction
     *
     * @return void
     */
    public function rollback(): void
    {
        DB::rollback();
    }
}
