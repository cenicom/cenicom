<?php

declare(strict_types=1);

namespace App\Modules\City\Permissions;

use App\Core\Generator\DTO\PermissionDefinition;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Definición de permisos del módulo CityPermissions.
 *
 * Archivo generado automáticamente por el CN Generator.
 * No modificar manualmente.
 */
final class CityPermissions
{
    /*
    |--------------------------------------------------------------------------
    | Constantes
    |--------------------------------------------------------------------------
    */

public const VIEW = 'cities.view';
public const CREATE = 'cities.create';
public const UPDATE = 'cities.update';
public const DELETE = 'cities.delete';

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
                'name' => 'cities',
                'action' => 'view',
                'permission' => 'cities.view',
                'group' => 'cities',
                'guard' => 'web',
                'description' => '',
            ]),

            PermissionDefinition::fromArray([
                'name' => 'cities',
                'action' => 'create',
                'permission' => 'cities.create',
                'group' => 'cities',
                'guard' => 'web',
                'description' => '',
            ]),

            PermissionDefinition::fromArray([
                'name' => 'cities',
                'action' => 'update',
                'permission' => 'cities.update',
                'group' => 'cities',
                'guard' => 'web',
                'description' => '',
            ]),

            PermissionDefinition::fromArray([
                'name' => 'cities',
                'action' => 'delete',
                'permission' => 'cities.delete',
                'group' => 'cities',
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
                'permission' => 'cities.view',
                'guard' => 'web',
                'description' => ''
            ],
[
                'permission' => 'cities.create',
                'guard' => 'web',
                'description' => ''
            ],
[
                'permission' => 'cities.update',
                'guard' => 'web',
                'description' => ''
            ],
[
                'permission' => 'cities.delete',
                'guard' => 'web',
                'description' => ''
            ]

        ];
    }
}
