<?php

declare(strict_types=1);

namespace Nauxa\RepositoryService\Abstracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Nauxa\RepositoryService\Contracts\RepositoryContract;

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
 * @package Nauxa\RepositoryService
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
     * The relationships to eager load.
     *
     * @var array
     */
    protected array $with = [];

    /**
     * {@inheritdoc}
     */
    public function find(int|string $id): ?Model
    {
        return $this->newQuery()->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findOrFail(int|string $id): Model
    {
        return $this->newQuery()->findOrFail($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findWhere(array $conditions): Collection
    {
        return $this->newQuery()->where($conditions)->get();
    }

    /**
     * {@inheritdoc}
     */
    public function findWhereIn(string $column, array $values): Collection
    {
        return $this->newQuery()->whereIn($column, $values)->get();
    }

    /**
     * {@inheritdoc}
     */
    public function all(): Collection
    {
        return $this->newQuery()->get();
    }

    /**
     * {@inheritdoc}
     */
    public function paginate(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator
    {
        return $this->newQuery()->paginate($perPage, $columns);
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
    public function firstOrCreate(array $attributes, array $values = []): Model
    {
        return $this->newQuery()->firstOrCreate($attributes, $values);
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
     * {@inheritdoc}
     */
    public function with(array|string $relations): static
    {
        $this->with = is_array($relations) ? $relations : func_get_args();

        return $this;
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

    /**
     * Create a new query builder instance with eager loading.
     *
     * @return Builder
     */
    protected function newQuery(): Builder
    {
        $query = $this->model->newQuery();

        if (!empty($this->with)) {
            $query->with($this->with);
        }

        return $query;
    }

    /**
     * Reset the eager loading relationships.
     *
     * @return static
     */
    public function resetWith(): static
    {
        $this->with = [];

        return $this;
    }
}
