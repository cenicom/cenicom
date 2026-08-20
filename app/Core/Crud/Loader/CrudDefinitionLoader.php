<?php

declare(strict_types=1);

namespace App\Core\Crud\Loader;

use App\Core\Contracts\Module\ModuleRegistryInterface;
use App\Core\Crud\Contracts\CrudDefinitionRegistryInterface;

final readonly class CrudDefinitionLoader
{
    public function __construct(
        private CrudDefinitionRegistryInterface $registry,
        private ModuleRegistryInterface $modules,
    ) {
    }

    /**
     * Carga las definiciones CRUD declaradas
     * por los módulos registrados.
     */
    public function load(): void
    {
        foreach ($this->modules->all() as $module) {
            if (! $module->enabled) {
                continue;
            }

            foreach ($module->crudDefinitions as $definition) {
                $this->registry->add($definition);
            }
        }
    }
}
