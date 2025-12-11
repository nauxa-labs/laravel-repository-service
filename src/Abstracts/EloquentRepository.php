<?php

declare(strict_types=1);

namespace Refinaldy\RepositoryService\Abstracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Refinaldy\RepositoryService\Contracts\RepositoryContract;

/**
 * Eloquent Repository Abstract Class
 *
 * Provides a base implementation for repository pattern using Eloquent ORM.
 * Extend this class and define the $model property to use standard CRUD operations.
 *
 * Example usage:
 * ```php
 * class UserRepositoryImplement extends EloquentRepository implements UserRepository
 * {
 *     protected Model $model;
 *
 *     public function __construct(User $model)
 *     {
 *         $this->model = $model;
 *     }
 * }
 * ```
 *
 * @package Refinaldy\RepositoryService
 */
abstract class EloquentRepository implements RepositoryContract
{
    /**
     * The Eloquent model instance.
     *
     * @var Model
     */
    protected Model $model;

    /**
     * {@inheritdoc}
     */
    public function find(int|string $id): ?Model
    {
        return $this->model->newQuery()->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findOrFail(int|string $id): Model
    {
        return $this->model->newQuery()->findOrFail($id);
    }

    /**
     * {@inheritdoc}
     */
    public function all(): Collection
    {
        return $this->model->newQuery()->get();
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $attributes): Model
    {
        return $this->model->newQuery()->create($attributes);
    }

    /**
     * {@inheritdoc}
     */
    public function update(int|string $id, array $attributes): bool
    {
        $record = $this->findOrFail($id);

        return $record->update($attributes);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(int|string $id): bool
    {
        $record = $this->findOrFail($id);

        return (bool) $record->delete();
    }

    /**
     * {@inheritdoc}
     */
    public function destroy(array $ids): int
    {
        return $this->model->newQuery()->whereIn(
            $this->model->getKeyName(),
            $ids
        )->delete();
    }

    /**
     * Get the underlying Eloquent model instance.
     *
     * @return Model
     */
    public function getModel(): Model
    {
        return $this->model;
    }

    /**
     * Set a new Eloquent model instance.
     *
     * @param Model $model
     * @return static
     */
    public function setModel(Model $model): static
    {
        $this->model = $model;

        return $this;
    }
}
