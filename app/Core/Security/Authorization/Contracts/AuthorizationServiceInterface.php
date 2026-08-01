<?php

declare(strict_types=1);

namespace App\Core\Security\Authorization\Contracts;

use App\Core\Security\Contracts\IdentityInterface;

interface AuthorizationServiceInterface
{
    /**
     * Determina si la identidad posee el permiso indicado.
     */
    public function can(
        IdentityInterface $identity,
        string $permission
    ): bool;
}
