<?php

declare(strict_types=1);

namespace App\Core\Services;

use App\Core\Contracts\SoftDeletableRepositoryInterface;
use App\Core\Contracts\SoftDeletableServiceInterface;

abstract class SoftDeletableBaseService extends BaseService implements SoftDeletableServiceInterface
{
    public function __construct(
        protected SoftDeletableRepositoryInterface $repository
    ) {
        parent::__construct($repository);
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
