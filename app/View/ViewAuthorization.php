<?php

declare(strict_types=1);

namespace App\View;

use App\Core\Security\Authorization\Contracts\PermissionResolverInterface;
use App\Core\Security\Contracts\IdentityInterface;
use App\View\Contracts\ViewAuthorizationInterface;

final readonly class ViewAuthorization implements ViewAuthorizationInterface
{
    public function __construct(
        private PermissionResolverInterface $permissionResolver,
    ) {
    }

    public function can(
        IdentityInterface $identity,
        string $permission,
    ): bool {
        return $this->permissionResolver->can(
            $identity,
            $permission,
        );
    }
}
