<?php

declare(strict_types=1);

namespace App\Core\Security\Permissions\Loader;

use App\Core\Security\Permissions\Contracts\PermissionDefinitionRegistryInterface;
use App\Modules\Institution\Security\InstitutionPermissionDefinition;
use App\Modules\Inventory\Security\InventoryPermissionDefinition;


final readonly class PermissionDefinitionLoader
{
    public function __construct(
        private PermissionDefinitionRegistryInterface $registry,
    ) {
    }

    /**
     * Carga las definiciones de permisos.
     */
    public function load(): void
    {
        // Las definiciones de los módulos se registrarán aquí.
        $this->registry->add(
            InstitutionPermissionDefinition::class
        );

        $this->registry->add(
            InventoryPermissionDefinition::class
        );
    }
}
