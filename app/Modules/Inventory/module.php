<?php

declare(strict_types=1);

return [
    'name' => 'Inventory',

    'namespace' => 'App\\Modules\\Inventory',

    'providers' => [],

    'permission_definitions' => [
        \App\Modules\Inventory\Security\InventoryPermissionDefinition::class,
    ],

    'navigation_definitions' => [
        \App\Modules\Inventory\Navigation\InventoryNavigation::class,
    ],

    'enabled' => true,
];
