<?php

declare(strict_types=1);

namespace App\Core\Security\Permissions;

use App\Core\Security\Permissions\Contracts\PermissionRegistryInterface;
use App\Core\Security\Permissions\DTO\PermissionDefinition;

final class PermissionRegistry implements PermissionRegistryInterface
{
    /**
     * @var array<string, PermissionDefinition>
     */
    private array $permissions = [];


    public function register(
        PermissionDefinition $permission
    ): void {
        $this->permissions[
            $permission->key()
        ] = $permission;
    }


    public function permission(
        string $name
    ): ?PermissionDefinition {
        return $this->permissions[$name] ?? null;
    }


    /**
     * @return array<string, PermissionDefinition>
     */
    public function all(): array
    {
        return $this->permissions;
    }


    public function clear(): void
    {
        $this->permissions = [];
    }
}
