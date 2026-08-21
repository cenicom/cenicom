<?php

declare(strict_types=1);

namespace App\Core\Crud;

use App\Core\Crud\Contracts\CrudPermissionRegistrarInterface;
use App\Core\Crud\Contracts\CrudPermissionServiceInterface;
use App\Core\Security\Permissions\Contracts\PermissionRegistrarInterface;

final readonly class CrudPermissionRegistrar implements
    CrudPermissionRegistrarInterface
{
    public function __construct(
        private CrudPermissionServiceInterface $permissions,
        private PermissionRegistrarInterface $registrar,
    ) {
    }

    /**
     * @return array<int, string>
     */
    public function register(
        string $resource,
        ?string $module = null,
    ): array {
        $permissions = $this->permissions->permissions(
            $resource
        );

        foreach ($permissions as $permission) {
            $this->registrar->register(
                $permission,
                module: $module,
            );
        }

        return $permissions;
    }
}
