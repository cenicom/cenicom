<?php

declare(strict_types=1);

namespace App\Core\Security\Authorization;

use App\Core\Security\Authorization\Contracts\RolePermissionResolverInterface;
use App\Core\Security\Contracts\IdentityInterface;
use App\Core\Security\Roles\Contracts\RoleRegistryInterface;


final readonly class RolePermissionResolver implements RolePermissionResolverInterface
{
    public function __construct(
        private RoleRegistryInterface $roleRegistry,
    ) {
    }

    /**
     * Determina si alguno de los roles de la identidad
     * concede el permiso solicitado.
     */
    public function hasRolePermission(
        IdentityInterface $identity,
        string $permission
    ): bool {
        if (! $identity->authenticated()) {
            return false;
        }

        foreach ($identity->roles() as $roleName) {
            $role = $this->roleRegistry->role($roleName);

            if ($role === null) {
                continue;
            }

            if (
                in_array(
                    $permission,
                    $role->permissions,
                    true
                )
            ) {
                return true;
            }
        }

        return false;
    }
}
