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
            'qualifiedServiceInterface' => $module->qualifiedServiceInterface(),
            'qualifiedRepositoryInterface' => $module->qualifiedRepositoryInterface(),
            'qualifiedModel' => $module->qualifiedModel(),
            'repositoryInterface' => $module->repositoryInterface(),
            'model' => $module->modelClass(),
            'variable' => $module->variable(),
            'imports' => $this->buildImports($module),
            'baseServiceClass' => $module->softDeletes()
                ? 'SoftDeletableBaseService'
                : 'BaseService',
        ];
    }

    /**
     * Construye las declaraciones de importación.
     */
    private function buildImports(ModuleData $module): string
    {
        $imports = [
            'use ' . $module->qualifiedRepositoryInterface() . ';',
            'use ' . $module->qualifiedServiceInterface() . ';',
        ];

        $imports[] = $module->softDeletes()
            ? 'use App\Core\Services\SoftDeletableBaseService;'
            : 'use App\Core\Services\BaseService;';

        return implode(PHP_EOL, $imports);
    }
}
