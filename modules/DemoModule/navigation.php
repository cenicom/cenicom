<?php

declare(strict_types=1);

use App\Core\Navigation\DTO\NavigationGroupData;
use App\Core\Navigation\DTO\NavigationItemData;

return [
    'groups' => [
        new NavigationGroupData(
            id: 'demo',
            label: 'Demo',
            icon: 'bi-box',
            order: 1,
        ),
    ],

    'items' => [
        new NavigationItemData(
            id: 'demo.dashboard',
            label: 'Dashboard Demo',
            route: 'demo.dashboard',
            icon: 'bi-speedometer',
            group: 'demo',
            order: 1,
        ),
    ],
];
