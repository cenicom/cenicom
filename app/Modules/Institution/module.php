<?php

declare(strict_types=1);

return [
    'name' => 'Institution',

    'namespace' => 'App\\Modules\\Institution',

    'providers' => [
        \App\Modules\Institution\Providers\InstitutionServiceProvider::class,
    ],

    'enabled' => true,
];
