<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface RepositoryInterface
{
    /**
     * Get all records
     *
     * @param array $columns
     * @return Collection
     */
    public function all(array $columns = ['*']): Collection;

    /**
     * Get all records with relationships
     *
     * @param array $relations
     * @return Collection
     */
    public function allWith(array $relations): Collection;

    /**
     * Find a record by ID
     *
     * @param int $id
     * @return Model|null
     */
    public function find(int $id): ?Model;

    /**
     * Find a record by ID or fail
     *
     * @param int $id
     * @return Model
     */
    public function findOrFail(int $id): Model;

    /**
     * Find a record by specific column
     *
     * @param string $column
     * @param mixed $value
     * @return Model|null
     */
    public function findBy(string $column, $value): ?Model;

    /**
     * Find records by specific column
     *
     * @param string $column
     * @param mixed $value
     * @return Collection
     */
    public function findAllBy(string $column, $value): Collection;

    /**
     * Create a new record
     *
     * @param array $data
     * @return Model
     */
    public function create(array $data): Model;

    /**
     * Update a record
     *
     * @param int $id
     * @param array $data
     * @return Model
     */
    public function update(int $id, array $data): Model;

    /**
     * Delete a record
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;

    /**
     * Get paginated records
     *
     * @param int $perPage
     * @param array $columns
     * @return LengthAwarePaginator
     */
    public function paginate(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator;

    /**
     * Get records with where clause
     *
     * @param string $column
     * @param mixed $value
     * @param string $operator
     * @return Collection
     */
    public function where(string $column, $value, string $operator = '='): Collection;

    /**
     * Get records with whereIn clause
     *
     * @param string $column
     * @param array $values
     * @return Collection
     */
    public function whereIn(string $column, array $values): Collection;

    /**
     * Count records
     *
     * @return int
     */
    public function count(): int;

    /**
     * Check if record exists
     *
     * @param int $id
     * @return bool
     */
    public function exists(int $id): bool;
}
