<?php

declare(strict_types=1);

namespace App\Core\Security\Roles\Contracts;

use App\Core\Security\Roles\DTO\RoleDefinition;

interface RoleRegistryInterface
{
    /**
     * Registra una definición de rol.
     */
    public function register(
        RoleDefinition $role
    ): void;

    /**
     * Obtiene un rol por nombre.
     */
    public function role(
        string $name
    ): ?RoleDefinition;

    /**
     * Obtiene todos los roles registrados.
     *
     * @return array<string, RoleDefinition>
     */
    public function all(): array;

    /**
     * Limpia el registro.
     */
    public function clear(): void;
}
