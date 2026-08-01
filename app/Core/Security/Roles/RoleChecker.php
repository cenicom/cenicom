<?php

declare(strict_types=1);

namespace App\Core\Security\Roles;

use App\Core\Security\Contracts\IdentityInterface;
use App\Core\Security\Roles\Contracts\RoleCheckerInterface;

final class RoleChecker implements RoleCheckerInterface
{
    /**
     * Determina si la identidad posee el rol indicado.
     */
    public function hasRole(
        IdentityInterface $identity,
        string $role
    ): bool {
        return $identity->authenticated()
            && in_array(
                $role,
                $identity->roles(),
                true
            );
    }
}
