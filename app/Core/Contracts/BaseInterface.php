<?php

declare(strict_types=1);

namespace App\Core\Repositories;

use App\Core\Contracts\RepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

abstract class BaseRepository implements RepositoryInterface
{
    protected Model $model;

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->newQuery()
            ->paginate($perPage);
    }

    public function findById(int|string $id): ?Model
    {
        return $this->model
            ->newQuery()
            ->find($id);
    }

    public function create(array $attributes): Model
    {
        return $this->model
            ->newQuery()
            ->create($attributes);
    }

    public function update(mixed $model, array $attributes): Model
    {
        $model->update($attributes);

        return $model->refresh();
    }

    public function delete(mixed $model): bool
    {
        return (bool) $model->delete();
    }
}
