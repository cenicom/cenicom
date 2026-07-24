<?php

declare(strict_types=1);

namespace App\Core\Generator\Processors;

use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\DTO\PermissionDefinition;


/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Procesador responsable de resolver, normalizar y validar
 * las definiciones de permisos de un módulo antes de ser
 * consumidas por PermissionGenerator.
 */
final class PermissionProcessor
{

    /**
     *  Procesa completamente la configuración de permisos del módulo.
     *
     * @return PermissionDefinition[]
     */
    public function process(ModuleData $module): array
    {
        $permissions = $this->resolvePermissions($module);

        $permissions = $this->normalize($permissions);

        $permissions = $this->removeDuplicates($permissions);

        $this->validate($permissions);

        return $permissions;
    }

    /**
     * Resuelve todas las definiciones de permisos del módulo.
     *
     * @return PermissionDefinition[]
     */
    private function resolvePermissions(ModuleData $module): array
    {
        $crud = $this->resolveCrudPermissions($module);

        $custom = $this->resolveCustomPermissions($module);

        return $this->mergePermissions(
            $crud,
            $custom
        );
    }

    /**
     * Resuelve los permisos CRUD estándar del módulo.
     *
     * @return PermissionDefinition[]
     */
    private function resolveCrudPermissions(
        ModuleData $module
    ): array {

        $permissions = [];

        $actions = [

            'view' => 'View records',

            'create' => 'Create records',

            'update' => 'Update records',

            'delete' => 'Delete records',

        ];

        foreach ($actions as $action => $description) {

            $permissions[] = new PermissionDefinition(

                name: sprintf(
                    '%s.%s',
                    $module->singular(),
                    $action
                ),

                action: $action,

                permission: sprintf(
                    '%s.%s',
                    $module->singular(),
                    $action
                ),

                group: $module->singular(),

                guard: 'web',

                description: $description,
            );
        }

        return $permissions;
    }

    /**
     * Resuelve los permisos personalizados definidos por el módulo.
     *
     * @return PermissionDefinition[]
     */
    private function resolveCustomPermissions(
        ModuleData $module
    ): array {

        return [];
    }

    /**
 * Definiciones personalizadas de permisos.
 *
 * @return array<int,array<string,mixed>>
 */
public function permissionDefinitions(): array
{
    return $this->definition['permissionDefinitions'] ?? [];
}

    /**
     * Fusiona las colecciones de permisos CRUD y personalizados.
     *
     * @param PermissionDefinition[] $crud
     * @param PermissionDefinition[] $custom
     *
     * @return PermissionDefinition[]
     */
    private function mergePermissions(
        array $crud,
        array $custom
    ): array {
        return array_merge(
            $crud,
            $custom
        );
    }

    /**
     * Normaliza la colección de permisos.
     *
     * @param PermissionDefinition[] $permissions
     *
     * @return PermissionDefinition[]
     */
    private function normalize(
        array $permissions
    ): array {

        $normalized = [];

        foreach ($permissions as $permission) {

            $normalized[] = new PermissionDefinition(

                /*
            |--------------------------------------------------------------------------
            | Identidad
            |--------------------------------------------------------------------------
            */

                name: $permission->name(),

                action: $this->normalizeAction(
                    $permission->action()
                ),

                /*
            |--------------------------------------------------------------------------
            | Definición
            |--------------------------------------------------------------------------
            */

                permission: $this->normalizePermissionName(
                    $permission->permission()
                ),

                group: $this->normalizeGroup(
                    $permission->group()
                ),

                guard: $this->normalizeGuard(
                    $permission->guard()
                ),

                /*
            |--------------------------------------------------------------------------
            | Metadata
            |--------------------------------------------------------------------------
            */

                description: $permission->description(),

                /*
            |--------------------------------------------------------------------------
            | Configuración
            |--------------------------------------------------------------------------
            */

                enabled: $permission->enabled(),

                generatePolicy: $permission->generatePolicy(),

                generateMiddleware: $permission->generateMiddleware(),

                generateMenu: $permission->generateMenu(),
            );
        }

        return $normalized;
    }

    /**
     * Elimina permisos duplicados de la colección.
     *
     * @param PermissionDefinition[] $permissions
     *
     * @return PermissionDefinition[]
     */
    private function removeDuplicates(
        array $permissions
    ): array {

        $unique = [];

        $seen = [];

        foreach ($permissions as $permission) {

            $key = $permission->permission();

            if (isset($seen[$key])) {
                continue;
            }

            $seen[$key] = true;

            $unique[] = $permission;
        }

        return $unique;
    }

    /**
     * Valida la colección completa de permisos.
     *
     * @param PermissionDefinition[] $permissions
     *
     * @throws '\InvalidArgumentException'
     */
    private function validate(
        array $permissions
    ): void {

        foreach ($permissions as $permission) {

            $this->validatePermission(
                $permission
            );
        }

        $this->validateUniquePermissions(
            $permissions
        );
    }

    /**
     * Valida una definición individual de permiso.
     *
     * @throws \InvalidArgumentException
     */
    private function validatePermission(
        PermissionDefinition $permission
    ): void {

        if ($permission->name() === '') {

            throw new \InvalidArgumentException(
                'Permission name cannot be empty.'
            );
        }

        if ($permission->action() === '') {

            throw new \InvalidArgumentException(
                'Permission action cannot be empty.'
            );
        }

        if ($permission->permission() === '') {

            throw new \InvalidArgumentException(
                'Permission identifier cannot be empty.'
            );
        }

        if ($permission->group() === '') {

            throw new \InvalidArgumentException(
                'Permission group cannot be empty.'
            );
        }

        if ($permission->guard() === '') {

            throw new \InvalidArgumentException(
                'Permission guard cannot be empty.'
            );
        }
    }

    /**
     * Valida que la colección no contenga permisos duplicados.
     *
     * @param PermissionDefinition[] $permissions
     *
     * @throws \InvalidArgumentException
     */
    private function validateUniquePermissions(
        array $permissions
    ): void {

        $seen = [];

        foreach ($permissions as $permission) {

            $key = $permission->permission();

            if (isset($seen[$key])) {

                throw new \InvalidArgumentException(
                    sprintf(
                        'Permission "%s" is duplicated.',
                        $key
                    )
                );
            }

            $seen[$key] = true;
        }
    }

    /**
     * Normaliza el guard del permiso.
     */
    private function normalizeGuard(
        string $guard
    ): string {

        $guard = trim($guard);

        if ($guard === '') {
            return 'web';
        }

        return strtolower($guard);
    }

    /**
     * Normaliza la acción del permiso.
     */
    private function normalizeAction(
        string $action
    ): string {

        return lcfirst(
            trim($action)
        );
    }

    /**
     * Normaliza el grupo del permiso.
     */
    private function normalizeGroup(
        string $group
    ): string {
        return strtolower(
            trim($group)
        );
    }

    /**
     * Normaliza el nombre completo del permiso.
     */
    private function normalizePermissionName(
        string $permission
    ): string {
        $permission = trim($permission);

        if ($permission === '') {
            return '';
        }

        $parts = explode('.', $permission, 2);

        if (count($parts) !== 2) {
            return strtolower($permission);
        }

        return sprintf(
            '%s.%s',
            strtolower(trim($parts[0])),
            $this->normalizeAction($parts[1])
        );
    }
}
