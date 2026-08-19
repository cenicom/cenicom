<?php

declare(strict_types=1);

namespace App\Core\Crud\Contracts;

interface CrudRegistrarInterface
{
    /**
     * Registra una definición CRUD.
     */
    public function register(
        string $resource,
        string $controller,
    ): void;
}
