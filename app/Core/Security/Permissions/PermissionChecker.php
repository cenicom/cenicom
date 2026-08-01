<?php

declare(strict_types=1);

namespace App\Core\Security\Permissions;

use App\Core\Security\Contracts\IdentityInterface;
use App\Core\Security\Permissions\Contracts\PermissionCheckerInterface;
use App\Core\Security\Permissions\Contracts\PermissionRegistryInterface;

final class PermissionChecker implements PermissionCheckerInterface
{
    public function __construct(
        private readonly PermissionRegistryInterface $registry
    ) {
    }


    public function can(
        IdentityInterface $identity,
        string $permission
    ): bool {

        if (! $identity->authenticated()) {
            return false;
        }

        $definition = $this->registry->permission(
            $permission
        );

        return $definition !== null;
    }
}
