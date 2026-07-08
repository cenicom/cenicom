<?php

declare(strict_types=1);

namespace App\Core\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

interface RepositoryInterface
{
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function findById(int|string $id): mixed;

    public function create(array $attributes): mixed;

    public function update(Model $model, array $attributes): Model;

    public function delete(Model $model): bool;
}
