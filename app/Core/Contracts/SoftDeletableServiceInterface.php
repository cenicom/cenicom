<?php

declare(strict_types=1);

namespace App\Core\Contracts;

interface SoftDeletableServiceInterface extends ServiceInterface
{
    public function restore(int|string $id): bool;

    public function forceDelete(int|string $id): bool;
}
