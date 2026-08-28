<?php

declare(strict_types=1);

namespace App\Core\Generator\Factories;

use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\DTO\PermissionDefinition;
use App\Core\Generator\DTO\PermissionMatrix;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Construye una PermissionMatrix completamente normalizada
 * a partir de un ModuleData.
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
 *
 * Es la única fuente de verdad para los permisos.
 */
final class PermissionMatrixFactory
{
    /**
     * Construye la matriz completa de permisos.
     */
    public static function build(ModuleData $module): PermissionMatrix
    {
        $permissions = [];

        $permissions = array_merge(
            $permissions,
            self::resolveCrudPermissions($module)
        );

        $permissions = array_merge(
            $permissions,
            self::resolveCustomPermissions($module)
        );

        $permissions = self::normalizePermissions($permissions);

        $permissions = self::removeDuplicates($permissions);

        return new PermissionMatrix($permissions);
    }

    /**
     * Genera permisos CRUD.
     *
     * @return PermissionDefinition[]
     */
    private static function resolveCrudPermissions(ModuleData $module): array
    {
        $permissions = [];

        foreach (self::crudActions() as $action) {
            $permissions[] = self::createPermissionDefinition(
                $module,
                $action
            );
        }

        return $permissions;
    }

    /**
     * Genera permisos personalizados.
     *
     * @return PermissionDefinition[]
     */
    private static function resolveCustomPermissions(ModuleData $module): array
    {
        $permissions = [];

        foreach (self::customPermissions($module) as $permission) {
            $permissions[] = self::createPermissionDefinition(
                $module,
                $permission
            );
        }

        return $permissions;
    }

    /**
     * Normaliza todas las definiciones.
     *
     * @param PermissionDefinition[] $permissions
     * @return PermissionDefinition[]
     */
    private static function normalizePermissions(array $permissions): array
    {
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
     * @return PermissionDefinition[]
     */
    private static function removeDuplicates(array $permissions): array
    {
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

    private static function createMatrix(array $permissions): PermissionMatrix
    {
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

    /**
     * Obtiene los permisos personalizados del módulo.
     *
     * @return string[]
     */
    private static function customPermissions(ModuleData $module): array
    {
        return $module->option(
            'custom_permissions',
            []
        );
    }

    private static function createPermissionDefinition(
        ModuleData $module,
        string $action,
    ): PermissionDefinition {
        $name = self::normalizeName(
            $module->name()
        );

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

    private static function normalizeName(string $value): string
    {
        $value = trim($value);

        $value = strtolower($value);

        $value = str_replace(
            [' ', '_'],
            '-',
            $value
        );

        return $value;
    }

    private static function normalizeGroup(string $group): string
    {
        return self::normalizeName($group);
    }

    private static function normalizeAction(
        string $action
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
