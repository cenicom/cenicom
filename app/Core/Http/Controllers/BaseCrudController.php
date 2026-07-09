<?php

declare(strict_types=1);

namespace App\Core\Http\Controllers;

use App\Core\Contracts\ServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

abstract class BaseCrudController
{
    public function __construct(
        protected ServiceInterface $service
    ) {
    }

    public function index(
        int $perPage = 15
    ): LengthAwarePaginator {
        return $this->service->paginate($perPage);
    }

    public function store(array $data): Model
    {
        return $this->service->create($data);
    }

    public function show(
        int|string $id
    ): ?Model {
        return $this->service->findById($id);
    }

    public function update(
        int|string $id,
        array $data
    ): bool {
        return $this->service->update(
            $id,
            $data
        );
    }

    public function destroy(
        int|string $id
    ): bool {
        return $this->service->delete($id);
    }
}
