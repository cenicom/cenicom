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
 * Genera automáticamente las pruebas de un módulo.
 *
 * Genera:
 *
 * - Feature Test
 * - Unit Test
 *
 * utilizando la información centralizada en ModuleData.
 *
 * @package App\Core\Generator\Generators
 * @since 1.0.0
 */
final class TestGenerator extends BaseGenerator
{
    /**
     * Determina si el generador aplica al módulo recibido.
     */
    public function supports(ModuleData $module): bool
    {
        return true;
    }


    /**
     * Genera las pruebas del módulo.
     */
    public function generate(ModuleData $module): GeneratorResult
    {
        $result = new GeneratorResult();

        $result->merge(
            $this->generateResult(
                'tests/unit.stub',
                $module->unitTestPath(),
                $this->buildUnitVariables($module)
            )
        );

        $result->merge(
            $this->generateResult(
                'tests/feature.stub',
                $module->featureTestPath(),
                $this->buildFeatureVariables($module)
            )
        );

        return $result;
    }

    /**
     * Variables Feature Test.
     *
     * @return array<string, string>
     */
    private function buildFeatureVariables(
        ModuleData $module
    ): array {

        return [

            'namespace'
            => $module->testNamespace(),

            'class'
            => $module->featureTestClass(),

            'modelClass'
            => $module->modelClass(),

            'qualifiedModel'
            => $module->qualifiedModel(),

            'routeName'
            => $module->routeName(),
        ];
    }


    /**
     * Variables Unit Test.
     *
     * @return array<string, string>
     */
    private function buildUnitVariables(
        ModuleData $module
    ): array {

        return [

            'namespace'
            => $module->testNamespace(),

            'class'
            => $module->unitTestClass(),

            'serviceClass'
            => $module->serviceClass(),

            'qualifiedService'
            => $module->qualifiedService(),
        ];
    }
}
