<?php

declare(strict_types=1);

namespace App\Core\Crud;

use App\Core\Crud\Contracts\CrudActionAuthorizationInterface;
use App\Core\Crud\Contracts\CrudPermissionResolverInterface;
use App\Core\Crud\DTO\CrudOperation;
use App\Core\Security\Authorization\Contracts\AuthorizationServiceInterface;
use App\Core\Security\Contracts\IdentityInterface;

final readonly class CrudActionAuthorization implements
    CrudActionAuthorizationInterface
{
    public function __construct(
        private AuthorizationServiceInterface $authorization,
        private CrudPermissionResolverInterface $permissionResolver,
    ) {
    }

    public function allows(
        IdentityInterface $identity,
        string $resource,
        CrudOperation $operation,
    ): bool {
        $permission = $this->permissionResolver->permission(
            $resource,
            $operation,
        );

        return $this->authorization->can(
            $identity,
            $permission,
        );
    }
}
