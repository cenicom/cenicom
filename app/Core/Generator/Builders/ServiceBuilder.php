<?php

declare(strict_types=1);

namespace App\Core\Generator\Builders;

use App\Core\Generator\DTO\ModuleData;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Construye las variables necesarias para generar
 * el Service de un módulo.
 *
 * Responsabilidades:
 *
 * - Construir namespace.
 * - Construir imports.
 * - Construir referencias de clases e interfaces.
 * - Preparar variables para el StubManager.
 */
final class ServiceBuilder
{
    /**
     * Construye todas las variables necesarias para el stub.
     *
     * @return array<string,string>
     */
    public function build(ModuleData $module): array
    {
        return [
            'namespace' => $module->serviceNamespace(),

            'service' => $module->serviceClass(),

            'serviceInterface' => $module->serviceInterface(),

            'qualifiedServiceInterface'
                => $module->qualifiedServiceInterface(),

            'qualifiedRepositoryInterface'
                => $module->qualifiedRepositoryInterface(),

            'qualifiedModel'
                => $module->qualifiedModel(),

            'repositoryInterface'
                => $module->repositoryInterface(),

            'model'
                => $module->modelClass(),

            'variable'
                => $module->variable(),

            'imports'
                => $this->buildImports($module),
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
                'use ' . $module->qualifiedRepositoryInterface() . ';',
                'use ' . $module->qualifiedServiceInterface() . ';',
                'use App\Core\Services\BaseService;',
            ]
        );
    }
}
