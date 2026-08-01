<?php

declare(strict_types=1);

namespace App\Core\Navigation\Contracts;

use App\Core\Security\Contracts\IdentityInterface;

interface NavigationPermissionResolverInterface
{
    /**
     * Determina si una identidad puede visualizar
     * un elemento de navegación.
     */
    public function canView(
        IdentityInterface $identity,
        ?string $permission
    ): bool;
}
