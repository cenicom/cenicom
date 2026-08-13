<?php

declare(strict_types=1);

namespace App\Core\Security\Permissions;

use App\Core\Security\Permissions\Contracts\PermissionDefinitionRegistryInterface;

final class PermissionDefinitionRegistry implements PermissionDefinitionRegistryInterface
{
    /**
     * @var array<int, class-string>
     */
    private array $definitions = [];

    /**
     * Agrega una definición de permisos.
     *
     * @param class-string $definition
     */
    public function add(string $definition): void
    {
        $this->definitions[] = $definition;
    }

    /**
     * Obtiene las definiciones registradas.
     *
     * @return array<int, class-string>
     */
    public function definitions(): array
    {
        return $this->definitions;
    }

    /**
     * Limpia las definiciones.
     */
    public function clear(): void
    {
        $this->definitions = [];
    }
}
