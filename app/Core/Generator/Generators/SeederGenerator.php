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
 * Genera automáticamente el Seeder de un módulo.
 *
 * Procesa el stub correspondiente utilizando la información
 * contenida en ModuleData y persiste el resultado mediante
 * la infraestructura común del CN Generator.
 *
 * @package App\Core\Generator\Generators
 * @since 1.0.0
 */
final class SeederGenerator extends BaseGenerator
{
    /**
     * Determina si el generador aplica al módulo recibido.
     */
    public function supports(ModuleData $module): bool
    {
        return true;
    }


    /**
     * Genera el Seeder del módulo.
     */
    public function generate(
        ModuleData $module
    ): GeneratorResult {

        $content = $this->render(
            'seeder.stub',
            $this->buildVariables($module)
        );


        $this->write(
            $module->seederPath(),
            $content
        );


        return (new GeneratorResult())
            ->addCreated(
                $module->seederPath()
            );
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
                => $module->seederNamespace(),

            'class'
                => $module->seederClass(),

            'modelNamespace'
                => $module->modelNamespace(),

            'modelClass'
                => $module->modelClass(),

            'factoryClass'
                => $module->factoryClass(),

            'qualifiedModel'
                => $module->qualifiedModel(),

            'qualifiedFactory'
                => $module->qualifiedFactory(),
        ];
    }
}
