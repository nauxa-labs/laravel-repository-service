<?php

declare(strict_types=1);

namespace Refinaldy\RepositoryService\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Repository Contract Interface
 *
 * Defines the standard CRUD operations for repository implementations.
 * All repository classes should implement this interface to ensure
 * consistent data access patterns across the application.
 *
 * @package Refinaldy\RepositoryService
 */
interface RepositoryContract
{
    /**
     * Retrieve a record by its primary key.
     *
     * @param int|string $id The primary key value
     * @return Model|null
     */
    public function find(int|string $id): ?Model;

    /**
     * Retrieve a record by its primary key or throw an exception.
     *
     * @param int|string $id The primary key value
     * @return Model
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findOrFail(int|string $id): Model;

    /**
     * Retrieve all records from the repository.
     *
     * @return Collection
     */
    public function all(): Collection;

    /**
     * Create a new record in the repository.
     *
     * @param array $attributes The attributes for the new record
     * @return Model
     */
    public function create(array $attributes): Model;

    /**
     * Update an existing record by its primary key.
     *
     * @param int|string $id The primary key value
     * @param array $attributes The attributes to update
     * @return bool
     */
    public function update(int|string $id, array $attributes): bool;

    /**
     * Delete a record by its primary key.
     *
     * @param int|string $id The primary key value
     * @return bool
     */
    public function delete(int|string $id): bool;

    /**
     * Delete multiple records by their primary keys.
     *
     * @param array $ids Array of primary key values
     * @return int Number of deleted records
     */
    public function destroy(array $ids): int;
}
