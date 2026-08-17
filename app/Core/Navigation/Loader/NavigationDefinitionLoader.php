<?php

declare(strict_types=1);

namespace App\Core\Navigation\Loader;

use App\Core\Contracts\Module\ModuleRegistryInterface;
use App\Core\Navigation\Registry\NavigationDefinitionRegistry;

final readonly class NavigationDefinitionLoader
{
    public function __construct(
        private NavigationDefinitionRegistry $registry,
        private ModuleRegistryInterface $modules,
    ) {}


    /**
     * Carga las definiciones de navegación.
     */
    public function load(): void
    {
        foreach ($this->modules->all() as $module) {
            if (! $module->enabled) {
                continue;
            }

            foreach ($module->navigationDefinitions as $definition) {
                $this->registry->add($definition);
            }
        }
    }
}
