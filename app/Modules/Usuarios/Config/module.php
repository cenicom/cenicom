<?php

return [

    'id' => 'usuarios',

    'name' => 'Usuarios',

    'description' => 'Administración de usuarios del sistema.',

    'version' => '1.0.0',

    'enabled' => true,

    'icon' => 'users',

    'color' => 'primary',

    'order' => 10,

    'providers' => [

        App\Modules\Usuarios\Providers\UsuariosServiceProvider::class,

    ],

    'navigation' => [

        [
            'title' => 'Usuarios',
            'route' => 'usuarios.index',
            'icon'  => 'users',
            'order' => 10,
        ],

    ],

    'permissions' => [

        'usuarios.view',

        'usuarios.create',

        'usuarios.edit',

        'usuarios.delete',

    ],

];
