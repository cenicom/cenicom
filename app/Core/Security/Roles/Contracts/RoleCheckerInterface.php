<?php

declare(strict_types=1);

namespace App\Core\Security\Roles\Contracts;

use App\Core\Security\Contracts\IdentityInterface;

interface RoleCheckerInterface
{
    /**
     * Determina si la identidad posee el rol indicado.
     */
    public function hasRole(
        IdentityInterface $identity,
        string $role
    ): bool;
}
