<?php

declare(strict_types=1);

return [
    'name' => 'Campus',

    'namespace' => 'App\\Modules\\Campus',

    'providers' => [
        \App\Modules\Campus\Providers\CampusServiceProvider::class,
    ],

    'enabled' => true,
];
