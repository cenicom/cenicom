<?php

declare(strict_types=1);

return [
    'name' => 'Campus',

    'namespace' => 'App\\Modules\\Campus',

    'providers' => [
        \App\Modules\Campus\Providers\CampusServiceProvider::class,
    ],

    'permission_definitions' => [
        \App\Modules\Campus\Security\CampusPermissionDefinition::class,
    ],

    'navigation_definitions' => [
        \App\Modules\Campus\Navigation\CampusNavigation::class,
    ],

    'view_definitions' => [
        \App\Modules\Campus\View\CampusView::class,
    ],

    'enabled' => true,
];
