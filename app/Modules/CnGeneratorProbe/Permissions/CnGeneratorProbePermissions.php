<?php

declare(strict_types=1);

namespace App\Modules\CnGeneratorProbe\Permissions;

use App\Core\Generator\DTO\PermissionDefinition;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Definición de permisos del módulo CnGeneratorProbePermissions.
 *
 * Archivo generado automáticamente por el CN Generator.
 * No modificar manualmente.
 */
final class CnGeneratorProbePermissions
{
    /*
    |--------------------------------------------------------------------------
    | Constantes
    |--------------------------------------------------------------------------
    */

public const VIEW = 'cn-generator-probes.view';
public const CREATE = 'cn-generator-probes.create';
public const UPDATE = 'cn-generator-probes.update';
public const DELETE = 'cn-generator-probes.delete';

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
                'name' => 'cn-generator-probes',
                'action' => 'view',
                'permission' => 'cn-generator-probes.view',
                'group' => 'cn-generator-probes',
                'guard' => 'web',
                'description' => '',
            ]),

            PermissionDefinition::fromArray([
                'name' => 'cn-generator-probes',
                'action' => 'create',
                'permission' => 'cn-generator-probes.create',
                'group' => 'cn-generator-probes',
                'guard' => 'web',
                'description' => '',
            ]),

            PermissionDefinition::fromArray([
                'name' => 'cn-generator-probes',
                'action' => 'update',
                'permission' => 'cn-generator-probes.update',
                'group' => 'cn-generator-probes',
                'guard' => 'web',
                'description' => '',
            ]),

            PermissionDefinition::fromArray([
                'name' => 'cn-generator-probes',
                'action' => 'delete',
                'permission' => 'cn-generator-probes.delete',
                'group' => 'cn-generator-probes',
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
                'permission' => 'cn-generator-probes.view',
                'guard' => 'web',
                'description' => ''
            ],
[
                'permission' => 'cn-generator-probes.create',
                'guard' => 'web',
                'description' => ''
            ],
[
                'permission' => 'cn-generator-probes.update',
                'guard' => 'web',
                'description' => ''
            ],
[
                'permission' => 'cn-generator-probes.delete',
                'guard' => 'web',
                'description' => ''
            ]

        ];
    }
}
