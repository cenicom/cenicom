<?php

declare(strict_types=1);

namespace App\Modules\Institution\Navigation;

use App\Core\Navigation\Contracts\NavigationDefinitionInterface;
use App\Core\Navigation\Contracts\NavigationRegistrarInterface;
use App\Core\Navigation\DTO\NavigationGroupData;
use App\Core\Navigation\DTO\NavigationItemData;


final readonly class InstitutionNavigation
implements NavigationDefinitionInterface
{
    public function register(
        NavigationRegistrarInterface $navigation
    ): void {

        //
        // La navegación del módulo se registrará aquí.
        //
        $navigation->group(
            new NavigationGroupData(
                id: 'administration',
                label: 'Administración',
                icon: 'bi bi-gear',
                order: 10,
            )
        );

        $navigation->item(
            new NavigationItemData(
                id: 'institutions',
                group: 'administration',
                label: 'Instituciones',
                icon: 'bi bi-building',
                route: 'institutions.index',
                order: 10,
            )
        );
    }
}
