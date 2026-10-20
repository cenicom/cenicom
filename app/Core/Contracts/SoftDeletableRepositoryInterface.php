<?php

declare(strict_types=1);

namespace App\Core\Contracts;

interface SoftDeletableRepositoryInterface extends RepositoryInterface
{
    public function restore(int|string $id): bool;

    public function forceDelete(int|string $id): bool;
}
