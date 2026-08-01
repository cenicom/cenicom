<?php

declare(strict_types=1);

namespace App\Core\Security\Authorization\Contracts;

use App\Core\Security\Contracts\IdentityInterface;

interface RolePermissionResolverInterface
{
    /**
     * Determina si alguno de los roles de la identidad
     * concede el permiso solicitado.
     */
    public function hasRolePermission(
        IdentityInterface $identity,
        string $permission
    ): bool;
}
