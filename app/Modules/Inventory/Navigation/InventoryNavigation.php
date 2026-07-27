<?php

declare(strict_types=1);

namespace App\Modules\Inventory\Navigation;

use App\Core\Navigation\Contracts\NavigationDefinitionInterface;
use App\Core\Navigation\Contracts\NavigationRegistrarInterface;
use App\Core\Navigation\DTO\NavigationGroupData;
use App\Core\Navigation\DTO\NavigationItemData;


final readonly class InventoryNavigation
implements NavigationDefinitionInterface
{
    public function register(
        NavigationRegistrarInterface $navigation
    ): void {

        $navigation->group(
            new NavigationGroupData(
                id: 'inventory',
                label: 'Inventario',
                icon: 'bi bi-box-seam',
                order: 20,
            )
        );


        $navigation->item(
            new NavigationItemData(
                id: 'products',
                group: 'inventory',
                label: 'Productos',
                icon: 'bi bi-box',
                route: 'products.index',
                order: 10,
            )
        );


        $navigation->item(
            new NavigationItemData(
                id: 'categories',
                group: 'inventory',
                label: 'Categorías',
                icon: 'bi bi-tags',
                route: 'categories.index',
                order: 20,
            )
        );


        $navigation->item(
            new NavigationItemData(
                id: 'movements',
                group: 'inventory',
                label: 'Movimientos',
                icon: 'bi bi-arrow-left-right',
                route: 'movements.index',
                order: 30,
            )
        );
    }
}
