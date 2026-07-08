<?php

declare(strict_types=1);

namespace App\Core\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface RepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function all(): Collection;

    public function findById(
        int|string $id,
        array $columns = ['*']
    ): ?Model;

    public function findOrFail(
        int|string $id,
        array $columns = ['*']
    ): Model;

    public function create(array $attributes): Model;

    public function update(
        int|string $id,
        array $attributes
    ): bool;

    public function delete(int|string $id): bool;

    public function restore(int|string $id): bool;

    public function forceDelete(int|string $id): bool;

    public function exists(int|string $id): bool;

    public function count(): int;
}
