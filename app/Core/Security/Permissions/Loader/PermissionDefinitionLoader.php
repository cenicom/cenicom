<?php

declare(strict_types=1);

namespace App\Core\Security\Permissions\Loader;

use App\Core\Contracts\Module\ModuleRegistryInterface;
use App\Core\Security\Permissions\Contracts\PermissionDefinitionRegistryInterface;

final readonly class PermissionDefinitionLoader
{
    public function __construct(
        private PermissionDefinitionRegistryInterface $registry,
        private ModuleRegistryInterface $modules,
    ) {
    }

    /**
     * Carga las definiciones de permisos declaradas
     * por los módulos registrados.
     */
    public function load(): void
    {
        foreach ($this->modules->all() as $module) {
            foreach ($module->permissionDefinitions as $definition) {
                $this->registry->add($definition);
            }
        }
    }
}
