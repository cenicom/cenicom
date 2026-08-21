<?php

declare(strict_types=1);

namespace App\Core\Crud\Contracts;

use App\Core\Crud\DTO\CrudOperation;

interface CrudRegistrarInterface
{
    /**
     * Registra una definición CRUD.
     *
     * @param array<int, CrudOperation> $operations
     */
    public function register(
        string $resource,
        string $controller,
        array $operations = [],
    ): void;

    /**
     * @return array<int, CrudOperation>
     */
    public function operations(
        string $resource
    ): array;

    public function hasOperation(
        string $resource,
        string $operation
    ): bool;
}
