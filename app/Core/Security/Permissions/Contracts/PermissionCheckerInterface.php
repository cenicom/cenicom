<?php

declare(strict_types=1);

namespace App\Core\Security\Permissions\Contracts;

use App\Core\Security\Contracts\IdentityInterface;



interface PermissionCheckerInterface
{
    /**
     * Determina si una identidad posee un permiso.
     */
    public function can(
        IdentityInterface $identity,
        string $permission
    ): bool;
}
