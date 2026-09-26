<?php

declare(strict_types=1);

return [
    'groups' => [
        [
            'id' => 'demo',
            'label' => 'Demo',
            'icon' => null,
            'order' => 1,
        ],
    ],

    'items' => [
        [
            'id' => 'demo.dashboard',
            'label' => 'Dashboard Demo',
            'route' => 'demo.dashboard',
            'permission' => null,
            'icon' => null,
            'order' => 1,
            'group' => 'demo',
        ],
    ],
];
