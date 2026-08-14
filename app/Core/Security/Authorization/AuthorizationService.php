<?php

declare(strict_types=1);

namespace App\Core\Security\Authorization;

use App\Core\Security\Authorization\Contracts\AuthorizationServiceInterface;
use App\Core\Security\Authorization\Contracts\PermissionResolverInterface;
use App\Core\Security\Contracts\IdentityInterface;
use App\Core\Security\Policies\Contracts\PolicyResolverInterface;


final readonly class AuthorizationService implements AuthorizationServiceInterface
{
    public function __construct(
        private PermissionResolverInterface $permissionResolver,
        private PolicyResolverInterface $policyResolver,
    ) {}

    /**
     * Determina si la identidad posee el permiso indicado.
     */
    public function can(
        IdentityInterface $identity,
        string $permission
    ): bool {
        return $this->permissionResolver->can(
            $identity,
            $permission
        );
    }

    public function allows(
        IdentityInterface $identity,
        string $policy,
        mixed $resource
    ): bool {
        $resolvedPolicy = $this->policyResolver->resolve(
            $policy
        );

        if ($resolvedPolicy === null) {
            return false;
        }

        return $resolvedPolicy->allows(
            $identity,
            $resource
        );
    }
}
