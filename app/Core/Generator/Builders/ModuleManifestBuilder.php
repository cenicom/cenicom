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
 * el manifiesto de un módulo.
 *
 * Responsabilidades:
 *
 * - Exponer los datos estructurales del módulo.
 * - Preparar las variables consumidas por el stub.
 */
final class ModuleManifestBuilder
{
    /**
     * Construye todas las variables necesarias para el stub.
     *
     * @return array<string,mixed>
     */
    public function build(ModuleData $module): array
    {
        return [
            'name' => $module->name(),

            'description' => $module->description(),

            'model' => $module->modelClass(),

            'routePrefix' => $module->routePrefix(),

            'routeName' => $module->routeName(),

            'permissions' => $module->permissions(),

            'menu' => $module->menu(),

            'api' => $module->api(),

            'tests' => $module->tests(),
        ];
    }
}
