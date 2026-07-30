<?php

return [
    'name' => 'TestModule',

    'namespace' => 'Tests\\Fixtures\\Modules\\TestModule',

    'providers' => [
        'Tests\\Fixtures\\Modules\\TestModule\\Providers\\FakeServiceProvider',
    ],

    'enabled' => true,
];
