<?php

declare(strict_types=1);

namespace App\Modules\Institution\Navigation;

use App\Core\Navigation\Contracts\NavigationDefinitionInterface;
use App\Core\Navigation\Contracts\NavigationRegistrarInterface;
use App\Core\Navigation\DTO\NavigationGroupData;
use App\Core\Navigation\DTO\NavigationItemData;

final readonly class InstitutionNavigation implements NavigationDefinitionInterface
{
    public function register(NavigationRegistrarInterface $navigation): void
    {
        $navigation->group(new NavigationGroupData(
            id: 'administration',
            label: 'Administración',
            icon: 'fa-solid fa-gear',
            order: 10,
        ));

        $navigation->item(new NavigationItemData(
            id: 'institutions',
            group: 'administration',
            label: 'Instituciones',
            icon: 'fa-solid fa-building',
            route: 'institutions.index',
            permission: 'institutions.view',
            order: 10,
        ));
    }
}