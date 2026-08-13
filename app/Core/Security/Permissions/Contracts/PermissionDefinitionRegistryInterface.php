<?php

declare(strict_types=1);

namespace App\Core\Security\Permissions\Contracts;

interface PermissionDefinitionRegistryInterface
{
    /**
     * Agrega una definición de permisos.
     *
     * @param class-string $definition
     */
    public function add(string $definition): void;

    /**
     * Obtiene las definiciones registradas.
     *
     * @return array<int, class-string>
     */
    public function definitions(): array;

    /**
     * Limpia las definiciones registradas.
     */
    public function clear(): void;
}
