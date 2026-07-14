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
    private const STUB = 'controller.stub';
    public function generate(
        ModuleData $module
    ): GeneratorResult {

        $path = $module->controllerPath();

        $this->generateFile(
            self::STUB,
            $path,
            $this->buildVariables($module)
        );

        return (new GeneratorResult())
            ->addCreated($path);
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

            'class'
            => $module->controllerClass(),

            'serviceNamespace'
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

            'modelClass'
            => $module->modelClass(),

            'routeName'
            => $module->routeName(),

            'viewPrefix'
            => $module->viewPrefix(),
        ];
    }
}
