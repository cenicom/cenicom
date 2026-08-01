<?php

declare(strict_types=1);

namespace App\Core\Security\Roles;

use App\Core\Security\Roles\Contracts\RoleRegistryInterface;
use App\Core\Security\Roles\DTO\RoleDefinition;

final class RoleRegistry implements RoleRegistryInterface
{
    /**
     * @var array<string, RoleDefinition>
     */
    private array $roles = [];

    /**
     * Registra una definición de rol.
     */
    public function register(
        RoleDefinition $role
    ): void {
        $this->roles[$role->name] = $role;
    }

    /**
     * Obtiene un rol por nombre.
     */
    public function role(
        string $name
    ): ?RoleDefinition {
        return $this->roles[$name] ?? null;
    }

    /**
     * Obtiene todos los roles registrados.
     *
     * @return array<string, RoleDefinition>
     */
    public function all(): array
    {
        return $this->roles;
    }

    /**
     * Limpia el registro.
     */
    public function clear(): void
    {
        $this->roles = [];
    }
}
