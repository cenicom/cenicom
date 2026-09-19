<?php

declare(strict_types=1);

namespace App\Modules\Currency\Security;

use App\Core\Generator\DTO\PermissionDefinition;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Definición de permisos del módulo CurrencyPermissions.
 *
 * Archivo generado automáticamente por el CN Generator.
 * No modificar manualmente.
 */
final class CurrencyPermissions
{
    /*
    |--------------------------------------------------------------------------
    | Constantes
    |--------------------------------------------------------------------------
    */

public const VIEW = 'currencies.view';
public const CREATE = 'currencies.create';
public const UPDATE = 'currencies.update';
public const DELETE = 'currencies.delete';

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
                'name' => 'currencies',
                'action' => 'view',
                'permission' => 'currencies.view',
                'group' => 'currencies',
                'guard' => 'web',
                'description' => '',
            ]),

            PermissionDefinition::fromArray([
                'name' => 'currencies',
                'action' => 'create',
                'permission' => 'currencies.create',
                'group' => 'currencies',
                'guard' => 'web',
                'description' => '',
            ]),

            PermissionDefinition::fromArray([
                'name' => 'currencies',
                'action' => 'update',
                'permission' => 'currencies.update',
                'group' => 'currencies',
                'guard' => 'web',
                'description' => '',
            ]),

            PermissionDefinition::fromArray([
                'name' => 'currencies',
                'action' => 'delete',
                'permission' => 'currencies.delete',
                'group' => 'currencies',
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
                'permission' => 'currencies.view',
                'guard' => 'web',
                'description' => ''
            ],
[
                'permission' => 'currencies.create',
                'guard' => 'web',
                'description' => ''
            ],
[
                'permission' => 'currencies.update',
                'guard' => 'web',
                'description' => ''
            ],
[
                'permission' => 'currencies.delete',
                'guard' => 'web',
                'description' => ''
            ]

        ];
    }
}
