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
     */
    public function can(
        IdentityInterface $identity,
        string $permission,
    ): bool {
        return $this->rolePermissionResolver->hasRolePermission(
            $identity,
            $permission,
        );
    }
}
