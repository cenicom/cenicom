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
 * Genera automáticamente el controlador de un módulo.
 *
 * Procesa el stub correspondiente utilizando la información
 * contenida en ModuleData y persiste el resultado mediante
 * la infraestructura común del CN Generator.
 *
 * @package App\Core\Generator\Generators
 * @since 1.0.0
 */
final class ControllerGenerator extends BaseGenerator
{

    private const STUB = 'controller.stub';

    /**
     * Determina si el generador aplica al módulo recibido.
     */
    public function supports(ModuleData $module): bool
    {
        return true;
    }

    /**
     * Genera el controlador del módulo.
     */
    public function generate(
        ModuleData $module
    ): GeneratorResult {

        $path = $module->controllerPath();

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
     * @return array<string, string>
     */
    private function buildVariables(
        ModuleData $module
    ): array {

        return [

            'namespace'
            => $module->controllerNamespace(),

            'controller'
            => $module->controllerClass(),

            'qualifiedServiceInterface'
            => $module->qualifiedServiceInterface(),

            'service'
            => $module->serviceNamespace(),

            'serviceClass'
            => $module->serviceClass(),

            'serviceInterface'
            => $module->serviceInterface(),

            'qualifiedService'
            => $module->qualifiedService(),

            'requestNamespace'
            => $module->requestNamespace(),

            'storeRequest'
            => $module->storeRequestClass(),

            'updateRequest'
            => $module->updateRequestClass(),

            'qualifiedStoreRequest'
            => $module->qualifiedStoreRequest(),

            'qualifiedUpdateRequest'
            => $module->qualifiedUpdateRequest(),

            'model'
            => $module->modelClass(),

            'routeName'
            => $module->routeName(),

            'viewPrefix'
            => $module->viewPrefix(),

            'qualifiedModel'
            => $module->qualifiedModel(),

            'variable'
            => $module->variable(),

            'pluralVariable'
            => $module->pluralVariable(),

            'singular'
            => $module->singular(),
        ];
    }
}
