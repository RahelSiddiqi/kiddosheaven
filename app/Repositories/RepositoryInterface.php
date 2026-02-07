<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface RepositoryInterface
{
    /**
     * Get all records
     */
    public function all(array $columns = ['*']): Collection;

    /**
     * Find a record by ID
     */
    public function find(int $id): ?Model;

    /**
     * Find a record by specific field
     */
    public function findBy(string $field, $value): ?Model;

    /**
     * Find records by specific field
     */
    public function findAllBy(string $field, $value): Collection;

    /**
     * Create a new record
     */
    public function create(array $data): Model;

    /**
     * Update a record
     */
    public function update(int $id, array $data): Model;

    /**
     * Delete a record
     */
    public function delete(int $id): bool;

    /**
     * Paginate records
     */
    public function paginate(int $perPage = 15);

    /**
     * Get records with relationships
     */
    public function with(array $relations);

    /**
     * Order records
     */
    public function orderBy(string $column, string $direction = 'asc');

    /**
     * Apply where conditions
     */
    public function where(array $conditions);
}
