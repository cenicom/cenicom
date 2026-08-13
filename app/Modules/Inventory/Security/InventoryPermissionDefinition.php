<?php

declare(strict_types=1);

namespace App\Modules\Inventory\Security;

use App\Core\Security\Permissions\Contracts\PermissionDefinitionInterface;
use App\Core\Security\Permissions\Contracts\PermissionRegistrarInterface;

final readonly class InventoryPermissionDefinition implements PermissionDefinitionInterface
{
    public function register(
        PermissionRegistrarInterface $permissions
    ): void {
        $permissions->register(
            name: 'inventory.products.view',
            description: 'Permite consultar productos.',
            module: 'inventory',
        );

        $permissions->register(
            name: 'inventory.categories.view',
            description: 'Permite consultar categorías.',
            module: 'inventory',
        );

        $permissions->register(
            name: 'inventory.movements.view',
            description: 'Permite consultar movimientos.',
            module: 'inventory',
        );
    }
}
