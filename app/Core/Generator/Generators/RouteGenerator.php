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
 * Genera automáticamente las rutas de un módulo.
 *
 * Procesa el stub correspondiente utilizando la información
 * contenida en ModuleData y persiste el resultado mediante
 * la infraestructura común del CN Generator.
 *
 * @package App\Core\Generator\Generators
 * @since 1.0.0
 */
final class RouteGenerator extends BaseGenerator
{
    private const STUB = 'route.stub';

    public function supports(ModuleData $module): bool
    {
        return true;
    }

    /**
     * Genera las rutas del módulo.
     */
    public function generate(
        ModuleData $module
    ): GeneratorResult {

        $path = $module->routePath();

        $result = new GeneratorResult();

        if (
            $this->generateFile(
                self::STUB,
                $path,
                $this->buildVariables($module)
            )
        ) {
            $result->addCreated($path);
        } else {
            $result->addSkipped($path);
        }

        return $result;
    }

    /**
     * Construye las variables utilizadas por el stub.
     *
     * @return array<string,string>
     */
    private function buildVariables(
        ModuleData $module
    ): array {

        return array_merge(
            $this->defaultVariables($module),
            [

                'qualifiedController'
                => $module->qualifiedController(),

                //'controllerClass'
                //=> $module->controllerClass(),

                'pluralVariable'
                => $module->pluralVariable(),

            ]
        );
    }
}
