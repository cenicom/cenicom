<?php

declare(strict_types=1);

namespace App\Modules\State\Permissions;

use App\Core\Generator\DTO\PermissionDefinition;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Definición de permisos del módulo StatePermissions.
 *
 * Archivo generado automáticamente por el CN Generator.
 * No modificar manualmente.
 */
final class StatePermissions
{
    /*
    |--------------------------------------------------------------------------
    | Constantes
    |--------------------------------------------------------------------------
    */

public const VIEW = 'states.view';
public const CREATE = 'states.create';
public const UPDATE = 'states.update';
public const DELETE = 'states.delete';

    /*
    |--------------------------------------------------------------------------
    | Definiciones
    |--------------------------------------------------------------------------
    */

    /**
     * @return PermissionDefinition[]
     */
    public static function definitions(): array
    {
        return [

            PermissionDefinition::fromArray([
                'name' => 'states',
                'action' => 'view',
                'permission' => 'states.view',
                'group' => 'states',
                'guard' => 'web',
                'description' => '',
            ]),

            PermissionDefinition::fromArray([
                'name' => 'states',
                'action' => 'create',
                'permission' => 'states.create',
                'group' => 'states',
                'guard' => 'web',
                'description' => '',
            ]),

            PermissionDefinition::fromArray([
                'name' => 'states',
                'action' => 'update',
                'permission' => 'states.update',
                'group' => 'states',
                'guard' => 'web',
                'description' => '',
            ]),

            PermissionDefinition::fromArray([
                'name' => 'states',
                'action' => 'delete',
                'permission' => 'states.delete',
                'group' => 'states',
                'guard' => 'web',
                'description' => '',
            ])

        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Array para Seeders
    |--------------------------------------------------------------------------
    */

    /**
     * @return array<int,array<string,mixed>>
     */
    public static function toArray(): array
    {
        return [

[
                'permission' => 'states.view',
                'guard' => 'web',
                'description' => ''
            ],
[
                'permission' => 'states.create',
                'guard' => 'web',
                'description' => ''
            ],
[
                'permission' => 'states.update',
                'guard' => 'web',
                'description' => ''
            ],
[
                'permission' => 'states.delete',
                'guard' => 'web',
                'description' => ''
            ]

        ];
    }
}
