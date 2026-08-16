<?php

declare(strict_types=1);

use App\Modules\Institution\Security\InstitutionPermissionDefinition;
use App\Modules\Inventory\Security\InventoryPermissionDefinition;

return [
    'name' => 'Permissions',
    'namespace' => 'Tests\\Fixtures\\Modules\\Permissions',

    'providers' => [],

    'permission_definitions' => [
        InstitutionPermissionDefinition::class,
        InventoryPermissionDefinition::class,
    ],

    'enabled' => true,
];
