<?php

declare(strict_types=1);

namespace App\Core\Crud\Contracts;

interface CrudDefinitionInterface
{
    /**
     * Registra la definición CRUD del módulo.
     */
    public function register(
        CrudRegistrarInterface $crud
    ): void;
}
