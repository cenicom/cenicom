<?php

declare(strict_types=1);

namespace App\Core\Generator\Support\Controller;

use App\Core\Generator\DTO\ModuleData;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Construye las variables necesarias para generar
 * el Controller del módulo.
 *
 * ControllerGenerator únicamente orquesta el proceso.
 */
final class ControllerBuilder
{
    /**
     * Construye las variables del stub del Controller.
     *
     * @return array<string,string>
     */
    public function build(ModuleData $module): array
    {
        return [
            'namespace'
            => $module->controllerNamespace(),

            'qualifiedServiceInterface'
            => $module->qualifiedServiceInterface(),

            'qualifiedStoreRequest'
            => $module->qualifiedStoreRequest(),

            'qualifiedUpdateRequest'
            => $module->qualifiedUpdateRequest(),

            'qualifiedModel'
            => $module->qualifiedModel(),

            'model'
            => $module->modelClass(),

            'controller'
            => $module->controllerClass(),

            'serviceInterface'
            => $module->serviceInterface(),

            'action' => $module->actionClass(),

            'qualifiedAction' => $module->qualifiedAction(),

            'viewPrefix'
            => $module->viewPrefix(),

            'pluralVariable'
            => $module->pluralVariable(),

            'storeRequest'
            => $module->storeRequestClass(),

            'updateRequest'
            => $module->updateRequestClass(),

            'routeName'
            => $module->routeName(),

            'variable'
            => $module->variable(),

            'displayName'
            => $module->displayName(),
        ];
    }
}
