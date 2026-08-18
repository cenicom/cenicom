<?php

declare(strict_types=1);

namespace App\Core\Generator\Builders;

use App\Core\Generator\DTO\ModuleData;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Construye todas las variables necesarias para generar
 * el Repository del módulo.
 *
 * Responsabilidades:
 *
 * - Construir namespace.
 * - Construir imports.
 * - Construir nombre de la clase.
 * - Preparar variables para el StubManager.
 */
final class RepositoryBuilder
{
    /**
     * Punto de entrada.
     *
     * @return array<string,string>
     */
    public function build(ModuleData $module): array
    {
        return [
            'namespace'
                => $module->repositoryNamespace(),

            'imports'
                => $this->buildImports($module),

            'repository'
                => $module->repositoryClass(),

            'repositoryInterface'
                => $module->repositoryInterface(),

            'qualifiedRepositoryInterface'
                => $module->qualifiedRepositoryInterface(),

            'qualifiedModel'
                => $module->qualifiedModel(),

            'model'
                => $module->modelClass(),

            'variable'
                => $module->variable(),
        ];
    }

    /**
     * Construye las declaraciones de importación.
     */
    private function buildImports(ModuleData $module): string
    {
        return implode(
            PHP_EOL,
            [
                'use ' . $module->qualifiedModel() . ';',
                'use ' . $module->qualifiedRepositoryInterface() . ';',
                'use Illuminate\Contracts\Pagination\LengthAwarePaginator;',
            ]
        );
    }
}
