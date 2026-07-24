<?php

declare(strict_types=1);

namespace App\Core\Generator\Generators;

use App\Core\Generator\BaseGenerator;
use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\Results\GeneratorResult;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Genera automáticamente la Factory Eloquent de un módulo.
 *
 * Procesa el stub correspondiente utilizando la información
 * contenida en ModuleData y persiste el resultado mediante
 * la infraestructura común del CN Generator.
 *
 * @package App\Core\Generator\Generators
 * @since 1.0.0
 */
final class FactoryGenerator extends BaseGenerator
{
    /**
     * Determina si el generador aplica al módulo recibido.
     */
    public function supports(ModuleData $module): bool
    {
        return true;
    }


    /**
     * Genera la Factory del módulo.
     */
    public function generate(
        ModuleData $module
    ): GeneratorResult {

        return $this->generateResult(
            'factory.stub',
            $module->factoryPath(),
            $this->buildVariables($module)
        );
    }


    /**
     * Construye las variables utilizadas por el stub.
     *
     * @return array<string, mixed>
     */
    private function buildVariables(
        ModuleData $module
    ): array {

        return [

            'namespace'
            => $module->factoryNamespace(),

            'factory'
            => $module->factoryClass(),

            'modelNamespace'
            => $module->modelNamespace(),

            'model'
            => $module->modelClass(),

            'qualifiedModel'
            => $module->qualifiedModel(),


        ];
    }
}
