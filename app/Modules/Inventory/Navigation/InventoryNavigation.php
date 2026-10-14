<?php

declare(strict_types=1);

namespace App\Modules\Inventory\Navigation;

use App\Core\Navigation\Contracts\NavigationDefinitionInterface;
use App\Core\Navigation\Contracts\NavigationRegistrarInterface;
use App\Core\Navigation\DTO\NavigationGroupData;
use App\Core\Navigation\DTO\NavigationItemData;

final readonly class InventoryNavigation implements NavigationDefinitionInterface
{
    public function register(
        NavigationRegistrarInterface $navigation
    ): void {
        $navigation->group(
            new NavigationGroupData(
                id: 'inventory',
                label: 'Inventario',
                icon: 'fa-solid fa-boxes-stacked',
                order: 20,
            )
        );

        $navigation->item(
            new NavigationItemData(
                id: 'products',
                group: 'inventory',
                label: 'Productos',
                icon: 'fa-solid fa-box',
                route: 'products.index',
                order: 10,
            )
        );

        $navigation->item(
            new NavigationItemData(
                id: 'categories',
                group: 'inventory',
                label: 'Categorías',
                icon: 'fa-solid fa-tags',
                route: 'categories.index',
                order: 20,
            )
        );

        $navigation->item(
            new NavigationItemData(
                id: 'movements',
                group: 'inventory',
                label: 'Movimientos',
                icon: 'fa-solid fa-right-left',
                route: 'movements.index',
                order: 30,
            )
        );
    }
}