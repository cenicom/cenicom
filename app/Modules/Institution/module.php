<?php

declare(strict_types=1);

return [
    'name' => 'Institution',

    'namespace' => 'App\\Modules\\Institution',

    'providers' => [
        \App\Modules\Institution\Providers\InstitutionServiceProvider::class,
    ],

    'permission_definitions' => [
        \App\Modules\Institution\Security\InstitutionPermissionDefinition::class,
    ],

    'navigation_definitions' => [
        \App\Modules\Institution\Navigation\InstitutionNavigation::class,
    ],

    'view_definitions' => [
        \App\Modules\Institution\View\InstitutionView::class,
    ],

    'enabled' => true,
];
