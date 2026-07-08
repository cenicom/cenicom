<?php

declare(strict_types=1);

namespace App\Core\Repositories;

use App\Core\Contracts\RepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository implements RepositoryInterface
{
    protected Model $model;

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->newQuery()
            ->paginate($perPage);
    }

    public function all(): Collection
    {
        return $this->model
            ->newQuery()
            ->get();
    }

    public function findById(
        int|string $id,
        array $columns = ['*']
    ): ?Model {
        return $this->model
            ->newQuery()
            ->find($id, $columns);
    }

    public function findOrFail(
        int|string $id,
        array $columns = ['*']
    ): Model {
        return $this->model
            ->newQuery()
            ->findOrFail($id, $columns);
    }

    public function create(array $attributes): Model
    {
        return $this->model
            ->newQuery()
            ->create($attributes);
    }

    public function update(
        int|string $id,
        array $attributes
    ): bool {
        $record = $this->findOrFail($id);

        return $record->update($attributes);
    }

    public function delete(int|string $id): bool
    {
        $record = $this->findOrFail($id);

        return (bool) $record->delete();
    }

    public function restore(int|string $id): bool
    {
        $record = $this->model
            ->newQuery()
            ->onlyTrashed()
            ->findOrFail($id);

        return $record->restore();
    }

    public function forceDelete(int|string $id): bool
    {
        $record = $this->model
            ->newQuery()
            ->onlyTrashed()
            ->findOrFail($id);

        return (bool) $record->forceDelete();
    }

    public function exists(int|string $id): bool
    {
        return $this->model
            ->newQuery()
            ->whereKey($id)
            ->exists();
    }

    public function count(): int
    {
        return $this->model
            ->newQuery()
            ->count();
    }
}
