<?php

declare(strict_types=1);

namespace App\Core\Security\Authorization;

use App\Core\Security\Authorization\Contracts\PermissionResolverInterface;
use App\Core\Security\Authorization\Contracts\RolePermissionResolverInterface;
use App\Core\Security\Contracts\IdentityInterface;

final readonly class PermissionResolver implements PermissionResolverInterface
{
    public function __construct(
        private RolePermissionResolverInterface $rolePermissionResolver,
    ) {
    }

    /**
     * Determina si una identidad posee un permiso.
     *
     * Un permiso puede proceder de:
     *
     * - asignación directa a la identidad;
     * - cualquiera de sus roles.
     */
    public function can(
        IdentityInterface $identity,
        string $permission,
    ): bool {
        /*
         * Permiso asignado directamente a la identidad.
         */
        if (
            in_array(
                $permission,
                $identity->permissions(),
                true
            )
        ) {
            return true;
        }

        /*
         * Permiso heredado mediante alguno de sus roles.
         *
         * RolePermissionResolver también controla
         * el caso de identidad invitada.
         */
        return $this->rolePermissionResolver->hasRolePermission(
            $identity,
            $permission,
        );
    }
}
