<?php

declare(strict_types=1);

namespace App\Core\View\Loader;

use App\Core\Contracts\Module\ModuleRegistryInterface;
use App\Core\View\Contracts\ViewDefinitionRegistryInterface;

final readonly class ViewDefinitionLoader
{
    public function __construct(
         private ViewDefinitionRegistryInterface $registry,
        private ModuleRegistryInterface $modules,
    ) {
    }

    /**
     * Carga las definiciones de vistas declaradas
     * por los módulos registrados.
     */
    public function load(): void
    {
        foreach ($this->modules->all() as $module) {
            if (! $module->enabled) {
                continue;
            }

            foreach ($module->viewDefinitions as $definition) {
                $this->registry->add($definition);
            }
        }
    }
}
