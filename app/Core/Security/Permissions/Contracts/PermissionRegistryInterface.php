<?php

declare(strict_types=1);

namespace App\Core\Security\Permissions\Contracts;

use App\Core\Security\Permissions\DTO\PermissionDefinition;

interface PermissionRegistryInterface
{
    /**
     * Registra una definición de permiso.
     */
    public function register(
        PermissionDefinition $permission
    ): void;


    /**
     * Obtiene un permiso por nombre.
     */
    public function permission(
        string $name
    ): ?PermissionDefinition;


    /**
     * Obtiene todos los permisos registrados.
     *
     * @return array<string, PermissionDefinition>
     */
    public function all(): array;


    /**
     * Limpia el registro.
     */
    public function clear(): void;
}
