<?php

declare(strict_types=1);

namespace App\Modules\GeneratorProbe\Security;

use App\Core\Generator\DTO\PermissionDefinition;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Definición de permisos del módulo GeneratorProbePermissions.
 *
 * Archivo generado automáticamente por el CN Generator.
 * No modificar manualmente.
 */
final class GeneratorProbePermissions
{
    /*
    |--------------------------------------------------------------------------
    | Constantes
    |--------------------------------------------------------------------------
    */

public const VIEW = 'generator-probes.view';
public const CREATE = 'generator-probes.create';
public const UPDATE = 'generator-probes.update';
public const DELETE = 'generator-probes.delete';

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
                'name' => 'generator-probes',
                'action' => 'view',
                'permission' => 'generator-probes.view',
                'group' => 'generator-probes',
                'guard' => 'web',
                'description' => '',
            ]),

            PermissionDefinition::fromArray([
                'name' => 'generator-probes',
                'action' => 'create',
                'permission' => 'generator-probes.create',
                'group' => 'generator-probes',
                'guard' => 'web',
                'description' => '',
            ]),

            PermissionDefinition::fromArray([
                'name' => 'generator-probes',
                'action' => 'update',
                'permission' => 'generator-probes.update',
                'group' => 'generator-probes',
                'guard' => 'web',
                'description' => '',
            ]),

            PermissionDefinition::fromArray([
                'name' => 'generator-probes',
                'action' => 'delete',
                'permission' => 'generator-probes.delete',
                'group' => 'generator-probes',
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
                'permission' => 'generator-probes.view',
                'guard' => 'web',
                'description' => ''
            ],
[
                'permission' => 'generator-probes.create',
                'guard' => 'web',
                'description' => ''
            ],
[
                'permission' => 'generator-probes.update',
                'guard' => 'web',
                'description' => ''
            ],
[
                'permission' => 'generator-probes.delete',
                'guard' => 'web',
                'description' => ''
            ]

        ];
    }
}
