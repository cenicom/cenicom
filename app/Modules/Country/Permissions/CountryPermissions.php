<?php

declare(strict_types=1);

namespace App\Modules\Country\Permissions;

use App\Core\Generator\DTO\PermissionDefinition;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Definición de permisos del módulo CountryPermissions.
 *
 * Archivo generado automáticamente por el CN Generator.
 * No modificar manualmente.
 */
final class CountryPermissions
{
    /*
    |--------------------------------------------------------------------------
    | Constantes
    |--------------------------------------------------------------------------
    */

public const VIEW = 'countries.view';
public const CREATE = 'countries.create';
public const UPDATE = 'countries.update';
public const DELETE = 'countries.delete';

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
                'name' => 'countries',
                'action' => 'view',
                'permission' => 'countries.view',
                'group' => 'countries',
                'guard' => 'web',
                'description' => '',
            ]),

            PermissionDefinition::fromArray([
                'name' => 'countries',
                'action' => 'create',
                'permission' => 'countries.create',
                'group' => 'countries',
                'guard' => 'web',
                'description' => '',
            ]),

            PermissionDefinition::fromArray([
                'name' => 'countries',
                'action' => 'update',
                'permission' => 'countries.update',
                'group' => 'countries',
                'guard' => 'web',
                'description' => '',
            ]),

            PermissionDefinition::fromArray([
                'name' => 'countries',
                'action' => 'delete',
                'permission' => 'countries.delete',
                'group' => 'countries',
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
                'permission' => 'countries.view',
                'guard' => 'web',
                'description' => ''
            ],
[
                'permission' => 'countries.create',
                'guard' => 'web',
                'description' => ''
            ],
[
                'permission' => 'countries.update',
                'guard' => 'web',
                'description' => ''
            ],
[
                'permission' => 'countries.delete',
                'guard' => 'web',
                'description' => ''
            ]

        ];
    }
}
