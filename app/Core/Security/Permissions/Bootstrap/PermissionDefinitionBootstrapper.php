<?php

declare(strict_types=1);

namespace App\Core\Security\Permissions\Bootstrap;

use App\Core\Security\Permissions\Contracts\PermissionDefinitionInterface;
use App\Core\Security\Permissions\Contracts\PermissionRegistrarInterface;
use App\Core\Security\Permissions\Contracts\PermissionDefinitionRegistryInterface;

final readonly class PermissionDefinitionBootstrapper
{
    public function __construct(
        private PermissionDefinitionRegistryInterface $definitions,
        private PermissionRegistrarInterface $registrar,
    ) {
    }

    /**
     * Ejecuta todas las definiciones de permisos registradas.
     */
    public function boot(): void
    {
        foreach ($this->definitions->definitions() as $definition) {
            $permission = app($definition);

            if (! $permission instanceof PermissionDefinitionInterface) {
                continue;
            }

            $permission->register(
                $this->registrar
            );
        }
    }
}
