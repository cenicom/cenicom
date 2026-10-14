<?php

declare(strict_types=1);

namespace App\Modules\Campus\Navigation;

use App\Core\Navigation\Contracts\NavigationDefinitionInterface;
use App\Core\Navigation\Contracts\NavigationRegistrarInterface;
use App\Core\Navigation\DTO\NavigationItemData;

final readonly class CampusNavigation implements NavigationDefinitionInterface
{
    public function register(
        NavigationRegistrarInterface $navigation
    ): void {
        $navigation->item(
            new NavigationItemData(
                id: 'campuses',
                label: 'Sedes',
                route: 'campuses.index',
                permission: 'campuses.view',
                icon: 'fa-solid fa-graduation-cap',
                order: 20,
                group: 'administration',
            )
        );
    }
}