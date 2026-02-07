<?php

namespace App\Services;

use App\Repositories\RepositoryInterface;

abstract class BaseService
{
    protected RepositoryInterface $repository;

    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get all records
     */
    public function all(array $columns = ['*'])
    {
        return $this->repository->all($columns);
    }

    /**
     * Find a record by ID
     */
    public function find(int $id)
    {
        return $this->repository->find($id);
    }

    /**
     * Find by field
     */
    public function findBy(string $field, $value)
    {
        return $this->repository->findBy($field, $value);
    }

    /**
     * Create a new record
     */
    public function create(array $data)
    {
        return $this->repository->create($data);
    }

    /**
     * Update a record
     */
    public function update(int $id, array $data)
    {
        return $this->repository->update($id, $data);
    }

    /**
     * Delete a record
     */
    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }

    /**
     * Paginate records
     */
    public function paginate(int $perPage = 15)
    {
        return $this->repository->paginate($perPage);
    }

    /**
     * Get with relationships
     */
    public function with(array $relations)
    {
        $this->repository->with($relations);

        return $this;
    }

    /**
     * Order by
     */
    public function orderBy(string $column, string $direction = 'asc')
    {
        $this->repository->orderBy($column, $direction);

        return $this;
    }

    /**
     * Apply where conditions
     */
    public function where(array $conditions)
    {
        $this->repository->where($conditions);

        return $this;
    }

    /**
     * Get the repository instance
     */
    public function getRepository(): RepositoryInterface
    {
        return $this->repository;
    }
}
