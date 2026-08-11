<?php

declare(strict_types=1);

namespace App\Core\Navigation\Authorization\Contracts;

use App\Core\Security\Contracts\IdentityInterface;

interface NavigationPermissionResolverInterface
{
    /**
     * Determina si una identidad puede visualizar
     * un elemento protegido de navegación.
     */
    public function canView(
        IdentityInterface $identity,
        ?string $permission,
    ): bool;
}
