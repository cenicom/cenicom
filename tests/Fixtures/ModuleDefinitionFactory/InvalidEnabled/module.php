<?php

declare(strict_types=1);

use Tests\Fixtures\Providers\BlogServiceProvider;

return [

    'name' => 'Blog',

    'namespace' => 'Tests\\Fixtures\\Modules\\Blog',

    'enabled' => 'yes',

    'providers' => [
        BlogServiceProvider::class,
    ],

];
