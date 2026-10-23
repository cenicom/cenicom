<?php

declare(strict_types=1);

return [
    'name' => 'academic',
    'namespace' => 'App\\Modules\\Academic',
    'providers' => [],
    'permission_definitions' => [
        \App\Modules\Academic\Security\AcademicPermissionDefinition::class,
    ],
    'navigation_definitions' => [],
    'enabled' => true,
];
