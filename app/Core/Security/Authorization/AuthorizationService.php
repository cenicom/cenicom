<?php

declare(strict_types=1);

namespace App\Core\Security\Authorization;

use App\Core\Security\Authorization\Contracts\AuthorizationServiceInterface;
use App\Core\Security\Contracts\IdentityInterface;
use App\Core\Security\Permissions\Contracts\PermissionCheckerInterface;

final readonly class AuthorizationService implements AuthorizationServiceInterface
{
    public function __construct(
        private PermissionCheckerInterface $permissionChecker,
    ) {
    }

    /**
     * Determina si la identidad posee el permiso indicado.
     */
    public function can(
        IdentityInterface $identity,
        string $permission
    ): bool {
        return $this->permissionChecker->can(
            $identity,
            $permission
        );
    }
}
