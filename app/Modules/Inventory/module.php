<?php

declare(strict_types=1);

return [
    'name' => 'Inventory',

    'namespace' => 'App\\Modules\\Inventory',

    'providers' => [],

    'permission_definitions' => [
        \App\Modules\Inventory\Security\InventoryPermissionDefinition::class,
    ],

    'enabled' => true,
];
