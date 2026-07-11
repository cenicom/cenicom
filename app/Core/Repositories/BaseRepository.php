<?php

declare(strict_types=1);

namespace App\Core\Repositories;

use App\Core\Contracts\RepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository implements RepositoryInterface
{
    public function __construct(
        protected Model $model
    ) {
    }

    /**
     * Obtiene una nueva consulta del modelo.
     */
    protected function query(): Builder
    {
        return $this->model->newQuery();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->query()
            ->paginate($perPage);
    }

    public function all(): Collection
    {
        return $this->query()
            ->get();
    }

    public function findById(
        int|string $id,
        array $columns = ['*']
    ): ?Model {
        return $this->query()
            ->find($id, $columns);
    }

    public function findOrFail(
        int|string $id,
        array $columns = ['*']
    ): Model {
        return $this->query()
            ->findOrFail($id, $columns);
    }

    public function create(array $attributes): Model
    {
        return $this->query()
            ->create($attributes);
    }

    public function update(
        int|string $id,
        array $attributes
    ): bool {
        return $this->findOrFail($id)
            ->update($attributes);
    }

    public function delete(int|string $id): bool
    {
        return (bool) $this->findOrFail($id)
            ->delete();
    }

    public function restore(int|string $id): bool
    {
        return $this->query()
            ->onlyTrashed()
            ->findOrFail($id)
            ->restore();
    }

    public function forceDelete(int|string $id): bool
    {
        return (bool) $this->query()
            ->onlyTrashed()
            ->findOrFail($id)
            ->forceDelete();
    }

    public function exists(int|string $id): bool
    {
        return $this->query()
            ->whereKey($id)
            ->exists();
    }

    public function count(): int
    {
        return $this->query()
            ->count();
    }
}
