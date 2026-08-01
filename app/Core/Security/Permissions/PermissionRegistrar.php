<?php

declare(strict_types=1);

namespace App\Core\Security\Permissions;

use App\Core\Security\Permissions\Contracts\PermissionRegistrarInterface;
use App\Core\Security\Permissions\Contracts\PermissionRegistryInterface;
use App\Core\Security\Permissions\DTO\PermissionDefinition;

final class PermissionRegistrar implements PermissionRegistrarInterface
{
    public function __construct(
        private readonly PermissionRegistryInterface $registry
    ) {
    }


    /**
     * Registra un nuevo permiso.
     */
    public function register(
        string $name,
        string $description = '',
        ?string $module = null,
    ): PermissionDefinition {

        $permission = new PermissionDefinition(
            name: $name,
            description: $description,
            module: $module,
        );

        $this->registry->register(
            $permission
        );

        return $permission;
    }
}
