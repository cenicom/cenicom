<?php

declare(strict_types=1);

return [
    'name' => 'Blog',

    'namespace' => 'Tests\\Fixtures\\Modules\\Blog',

    'providers' => [
        Tests\Fixtures\Providers\BlogServiceProvider::class,
        Tests\Fixtures\Providers\UsersServiceProvider::class,
    ],
];
