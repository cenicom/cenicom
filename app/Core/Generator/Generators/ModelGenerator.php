<?php

declare(strict_types=1);

namespace App\Core\Generator\Generators;


use App\Core\Generator\BaseGenerator;
use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\GeneratorResult;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Genera automáticamente el modelo Eloquent de un módulo.
 *
 * Procesa el stub correspondiente utilizando la información
 * contenida en ModuleData y persiste el resultado mediante
 * FileWriter.
 *
 * @package App\Core\Generator\Generators
 * @since 1.0.0
 */
final class ModelGenerator extends BaseGenerator
{

    public function supports(ModuleData $module): bool
    {
        return true;
    }

    /**
     * Genera el modelo del módulo.
     */
    public function generate(ModuleData $module): GeneratorResult
    {
        $content = $this->stubManager->render(
            'model.stub',
            $this->buildVariables($module)
        );

        $this->fileWriter->write(
            $module->modelPath(),
            $content
        );

        return (new GeneratorResult())
            ->addCreated(
                $module->modelPath()
            );
    }

    /**
     * Construye las variables utilizadas por el stub.
     *
     * @return array<string,string>
     */
    private function buildVariables(ModuleData $module): array
    {
        return [

            'namespace'
            => $module->modelNamespace(),

            'class'
            => $module->modelClass(),

            'table'
            => $module->table(),

            'fillable'
            => '',

            'casts'
            => '',
        ];
    }


}
