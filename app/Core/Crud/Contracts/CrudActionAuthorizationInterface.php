<?php

declare(strict_types=1);

namespace App\Core\Crud\Contracts;

use App\Core\Crud\DTO\CrudOperation;
use App\Core\Security\Contracts\IdentityInterface;

interface CrudActionAuthorizationInterface
{
    /**
     * Determina si una identidad está autorizada
     * para ejecutar una operación CRUD sobre un recurso.
     */
    public function allows(
        IdentityInterface $identity,
        string $resource,
        CrudOperation $operation,
    ): bool;
}
