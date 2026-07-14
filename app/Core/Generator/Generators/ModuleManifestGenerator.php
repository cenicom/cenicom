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
 * Genera automáticamente el manifiesto de un módulo.
 *
 * El manifiesto contiene la definición estructural,
 * configuración y capacidades del módulo generado.
 *
 * @package App\Core\Generator\Generators
 * @since 1.0.0
 */
final class ModuleManifestGenerator extends BaseGenerator
{
    /**
     * Determina si el generador aplica al módulo recibido.
     */
    public function supports(ModuleData $module): bool
    {
        return true;
    }


    /**
     * Genera el manifiesto del módulo.
     */
    public function generate(
        ModuleData $module
    ): GeneratorResult {

        $content = $this->render(
            'module-manifest.stub',
            $this->buildVariables($module)
        );


        $this->write(
            $module->moduleManifestPath(),
            $content
        );


        return (new GeneratorResult())
            ->addCreated(
                $module->moduleManifestPath()
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

            'name'
                => $module->name(),

            'description'
                => $module->description(),

            'model'
                => $module->modelClass(),

            'routePrefix'
                => $module->routePrefix(),

            'routeName'
                => $module->routeName(),

            'permissions'
                => $module->permissions(),

            'menu'
                => $module->menu(),

            'api'
                => $module->api(),

            'tests'
                => $module->tests(),
        ];
    }
}
