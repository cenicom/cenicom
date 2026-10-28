<?php

return [
    'name' => 'academic',
    'namespace' => 'App\\Modules\\Academic',
    'providers' => [
        \App\Modules\Academic\Providers\AcademicServiceProvider::class,
    ],
    'permission_definitions' => [
        \App\Modules\Academic\Security\AcademicPermissionDefinition::class,
    ],
    'navigation_definitions' => [],
    'enabled' => true,
];
