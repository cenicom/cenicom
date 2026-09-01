<?php

declare(strict_types=1);

namespace App\Core\Generator\Factories;

use App\Core\Generator\DTO\PermissionDefinition;
use App\Core\Generator\DTO\PermissionMatrix;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Construye una PermissionMatrix completamente normalizada
 * a partir de los datos necesarios del módulo.
 *
 * Responsabilidades:
 *
 * - Resolver permisos CRUD.
 * - Resolver permisos personalizados.
 * - Normalizar nombres.
 * - Eliminar duplicados.
 * - Construir la PermissionMatrix.
 *
 * No genera archivos.
 * No conoce stubs.
 * No conoce Laravel.
 * No depende de ModuleData.
 *
 * Es la única fuente de verdad para los permisos generados.
 */
final class PermissionMatrixFactory
{
    /**
     * Construye la matriz completa de permisos.
     *
     * @param string $moduleName
     * @param string[] $customPermissions
     */
    public static function build(
        string $moduleName,
        array $customPermissions = [],
    ): PermissionMatrix {
        $moduleName = self::normalizeName($moduleName);

        if ($moduleName === '') {
            return new PermissionMatrix([]);
        }

        $permissions = [];

        $permissions = array_merge(
            $permissions,
            self::resolveCrudPermissions($moduleName)
        );

        $permissions = array_merge(
            $permissions,
            self::resolveCustomPermissions(
                $moduleName,
                $customPermissions
            )
        );

        $permissions = self::normalizePermissions($permissions);

        $permissions = self::removeDuplicates($permissions);

        return self::createMatrix($permissions);
    }

    /**
     * Genera permisos CRUD.
     *
     * @return PermissionDefinition[]
     */
    private static function resolveCrudPermissions(
        string $moduleName,
    ): array {
        $permissions = [];

        foreach (self::crudActions() as $action) {
            $permissions[] = self::createPermissionDefinition(
                $moduleName,
                $action
            );
        }

        return $permissions;
    }

    /**
     * Genera permisos personalizados.
     *
     * @param string $moduleName
     * @param string[] $customPermissions
     *
     * @return PermissionDefinition[]
     */
    private static function resolveCustomPermissions(
        string $moduleName,
        array $customPermissions,
    ): array {
        $permissions = [];

        foreach ($customPermissions as $permission) {
            $permissions[] = self::createPermissionDefinition(
                $moduleName,
                $permission
            );
        }

        return $permissions;
    }

    /**
     * Normaliza todas las definiciones.
     *
     * @param PermissionDefinition[] $permissions
     *
     * @return PermissionDefinition[]
     */
    private static function normalizePermissions(
        array $permissions,
    ): array {
        $normalized = [];

        foreach ($permissions as $permission) {
            $normalized[] = self::normalizePermission($permission);
        }

        return $normalized;
    }

    /**
     * Elimina permisos duplicados.
     *
     * @param PermissionDefinition[] $permissions
     *
     * @return PermissionDefinition[]
     */
    private static function removeDuplicates(
        array $permissions,
    ): array {
        $unique = [];
        $seen = [];

        foreach ($permissions as $permission) {
            $key = self::permissionKey($permission);

            if (isset($seen[$key])) {
                continue;
            }

            $seen[$key] = true;
            $unique[] = $permission;
        }

        return $unique;
    }

    /**
     * Construye la matriz de permisos.
     *
     * @param PermissionDefinition[] $permissions
     */
    private static function createMatrix(
        array $permissions,
    ): PermissionMatrix {
        return new PermissionMatrix($permissions);
    }

    /**
     * Devuelve las acciones CRUD base.
     *
     * @return string[]
     */
    private static function crudActions(): array
    {
        return [
            'view',
            'create',
            'update',
            'delete',
        ];
    }

    private static function createPermissionDefinition(
        string $moduleName,
        string $action,
    ): PermissionDefinition {
        $name = self::normalizeName($moduleName);

        $group = $name;

        return PermissionDefinition::fromArray([
            'name' => $name,
            'action' => $action,
            'permission' => self::buildPermissionName(
                $group,
                $action
            ),
            'group' => $group,
        ]);
    }

    private static function normalizePermission(
        PermissionDefinition $permission,
    ): PermissionDefinition {
        $definition = $permission->toArray();

        $definition['name'] = self::normalizeName(
            $definition['name']
        );

        $definition['group'] = self::normalizeGroup(
            $definition['group']
        );

        $definition['action'] = self::normalizeAction(
            $definition['action']
        );

        $definition['permission'] = self::buildPermissionName(
            $definition['group'],
            $definition['action']
        );

        return PermissionDefinition::fromArray($definition);
    }

    private static function normalizeName(
        string $value,
    ): string {
        $value = trim($value);

        $value = strtolower($value);

        $value = str_replace(
            [' ', '_'],
            '-',
            $value
        );

        return $value;
    }

    private static function normalizeGroup(
        string $group,
    ): string {
        return self::normalizeName($group);
    }

    private static function normalizeAction(
        string $action,
    ): string {
        return self::normalizeName($action);
    }

    private static function buildPermissionName(
        string $group,
        string $action,
    ): string {
        return sprintf(
            '%s.%s',
            $group,
            $action
        );
    }

    /**
     * Obtiene la clave única de un permiso.
     */
    private static function permissionKey(
        PermissionDefinition $permission,
    ): string {
        return $permission->permission();
    }
}
