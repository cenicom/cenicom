<?php

declare(strict_types=1);

namespace App\Core\Generator\Support\Permissions;

use App\Core\Generator\DTO\ModuleData;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Construye todas las variables relacionadas con permisos
 * utilizadas por PermissionGenerator.
 *
 * Responsabilidades:
 *
 * - Resolver permisos del módulo.
 * - Construir definiciones de permisos.
 * - Construir arreglos de permisos.
 * - Preparar variables para los stubs.
 *
 * PermissionGenerator únicamente orquesta el proceso.
 */
final class PermissionBuilder
{
    /**
     * Punto de entrada.
     *
     * Retorna todas las variables necesarias para el StubManager.
     */
    public function build(ModuleData $module): array
    {
        return [

            'namespace' => $module->permissionNamespace(),

            'module' => $module->name(),

            'imports' => $this->buildImports($module),

            'moduleName' => $module->permissionClass(),

            'constants' => $this->buildConstants($module),

            'permissionDefinitions'
            => $this->buildPermissionDefinitions($module),

            'permissionArray'
            => $this->buildPermissionArray($module),

        ];
    }

    private function buildConstants(ModuleData $module): string
    {
        $matrix = $module->permissionMatrix();

        if ($matrix === null) {
            return '';
        }

        $constants = [];

        foreach ($matrix->permissions() as $permission) {

            $constants[] = sprintf(
                "public const %s = '%s';",
                strtoupper($permission->action()),
                $permission->permission()
            );
        }

        return implode("\n", $constants);
    }

    private function buildImports(ModuleData $module): string
    {
        return implode("\n", [
            'use App\Core\Generator\DTO\PermissionDefinition;',
        ]);
    }

    /**
     * Construye las definiciones (constantes, bloques, etc.).
     */
    private function buildPermissionDefinitions(ModuleData $module): string
    {
        $matrix = $module->permissionMatrix();

        if ($matrix === null) {
            return '';
        }

        $items = [];

        foreach ($matrix->permissions() as $permission) {

            $items[] = sprintf(
                "            PermissionDefinition::fromArray([
                'name' => '%s',
                'action' => '%s',
                'permission' => '%s',
                'group' => '%s',
                'guard' => '%s',
                'description' => '%s',
            ])",
                $permission->name(),
                $permission->action(),
                $permission->permission(),
                $permission->group(),
                $permission->guard(),
                $permission->description() ?? ''
            );
        }

        return implode(",\n\n", $items);
    }

    /**
     * Construye el arreglo de permisos.
     */
    private function buildPermissionArray(ModuleData $module): string
    {
        $matrix = $module->permissionMatrix();

        if ($matrix === null) {
            return '';
        }

        $items = [];

        foreach ($matrix->permissions() as $permission) {

            $items[] = sprintf(
                "[
                'permission' => '%s',
                'guard' => '%s',
                'description' => '%s'
            ]",
                $permission->permission(),
                $permission->guard(),
                $permission->description() ?? ''
            );
        }

        return implode(",\n", $items);
    }

    /**
     * Genera el nombre de una constante.
     */
    private function buildPermissionConstant(string $permission): string
    {
        return '';
    }

    /**
     * Genera la etiqueta legible del permiso.
     */
    private function buildPermissionLabel(string $permission): string
    {
        return '';
    }

    /**
     * Genera la descripción del permiso.
     */
    private function buildPermissionDescription(string $permission): string
    {
        return '';
    }

    /**
     * Normaliza el nombre interno del permiso.
     */
    private function normalizePermission(string $permission): string
    {
        return $permission;
    }
}
