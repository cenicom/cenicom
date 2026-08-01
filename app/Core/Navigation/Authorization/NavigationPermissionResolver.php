<?php

declare(strict_types=1);

namespace App\Core\Navigation\Authorization;


use App\Core\Navigation\Contracts\NavigationPermissionResolverInterface;
use App\Core\Security\Authorization\Contracts\PermissionResolverInterface;
use App\Core\Security\Contracts\IdentityInterface;

final readonly class NavigationPermissionResolver implements NavigationPermissionResolverInterface
{
    public function __construct(
        private PermissionResolverInterface $permissionResolver,
    ) {
    }

    /**
     * Determina si una identidad puede visualizar
     * un elemento de navegación.
     */
    public function canView(
        IdentityInterface $identity,
        ?string $permission
    ): bool {
        /*
         * Un nodo sin permiso asociado
         * es visible para cualquier identidad.
         */
        if ($permission === null) {
            return true;
        }

        return $this->permissionResolver->can(
            $identity,
            $permission
        );
    }
}
