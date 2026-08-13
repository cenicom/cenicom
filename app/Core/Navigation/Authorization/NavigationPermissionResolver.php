<?php

declare(strict_types=1);

namespace App\Core\Navigation\Authorization;

use App\Core\Navigation\Contracts\NavigationPermissionResolverInterface;
use App\Core\Security\Authorization\Contracts\AuthorizationServiceInterface;
use App\Core\Security\Contracts\IdentityInterface;

final readonly class NavigationPermissionResolver
    implements NavigationPermissionResolverInterface
{
    public function __construct(
        private AuthorizationServiceInterface $authorization,
    ) {
    }

    /**
     * Determina si una identidad puede visualizar
     * un elemento protegido de navegación.
     */
    public function canView(
        IdentityInterface $identity,
        ?string $permission,
    ): bool {
        if ($permission === null) {
            return true;
        }

        return $this->authorization->can(
            $identity,
            $permission,
        );
    }
}
