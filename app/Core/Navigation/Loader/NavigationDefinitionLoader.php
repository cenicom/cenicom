<?php

declare(strict_types=1);

namespace App\Core\Navigation\Loader;

use App\Core\Navigation\Registry\NavigationDefinitionRegistry;

final readonly class NavigationDefinitionLoader
{
    public function __construct(
        private NavigationDefinitionRegistry $registry,
    ) {}


    /**
     * Carga las definiciones de navegación.
     */
    public function load(): void
    {
        $this->registry->add(
            \App\Modules\Institution\Navigation\InstitutionNavigation::class
        );

        $this->registry->add(
            \App\Modules\Inventory\Navigation\InventoryNavigation::class
        );
    }
}
