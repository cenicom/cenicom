<?php

declare(strict_types=1);

namespace App\Core\Services;

use App\Core\Contracts\RepositoryInterface;
use App\Core\Contracts\ServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

abstract class BaseService implements ServiceInterface
{
    public function __construct(
        protected RepositoryInterface $repository
    ) {
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage);
    }

    public function all(): Collection
    {
        return $this->repository->all();
    }

    public function findById(
        int|string $id,
        array $columns = ['*']
    ): ?Model {
        return $this->repository->findById($id, $columns);
    }

    public function create(array $attributes): Model
    {
        return $this->repository->create($attributes);
    }

    public function update(
        int|string $id,
        array $attributes
    ): bool {
        return $this->repository->update($id, $attributes);
    }

    public function delete(int|string $id): bool
    {
        return $this->repository->delete($id);
    }

    public function restore(int|string $id): bool
    {
        return $this->repository->restore($id);
    }

    public function forceDelete(int|string $id): bool
    {
        return $this->repository->forceDelete($id);
    }
}
