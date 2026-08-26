<?php

declare(strict_types=1);

namespace App\View\Contracts;

use App\Core\Security\Contracts\IdentityInterface;

interface ViewAuthorizationInterface
{
    /**
     * Determina si la identidad actual puede utilizar
     * una capacidad protegida por permiso.
     */
    public function can(
        IdentityInterface $identity,
        string $permission,
    ): bool;
}
