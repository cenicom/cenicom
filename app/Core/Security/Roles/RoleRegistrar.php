<?php

declare(strict_types=1);

namespace App\Core\Security\Roles;

use App\Core\Security\Roles\Contracts\RoleRegistrarInterface;
use App\Core\Security\Roles\Contracts\RoleRegistryInterface;
use App\Core\Security\Roles\DTO\RoleDefinition;

final readonly class RoleRegistrar implements RoleRegistrarInterface
{
    public function __construct(
        private RoleRegistryInterface $registry
    ) {
    }

    /**
     * Registra un rol en el sistema.
     *
     * @param array<int, string> $permissions
     */
    public function register(
        string $name,
        string $label,
        array $permissions = []
    ): RoleDefinition {
        $role = new RoleDefinition(
            name: $name,
            label: $label,
            permissions: $permissions,
        );

        $this->registry->register($role);

        return $role;
    }
}
