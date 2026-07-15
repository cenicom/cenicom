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
        $this->generateFile(
            'model.stub',
            $module->modelPath(),
            $this->buildVariables($module)
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
            'namespace' => $module->modelNamespace(),

            'model' => $module->modelClass(),

            'description' => $module->description(),

            'table' => $module->table(),

            'fillable' => $this->buildFillable($module),

            'casts' => $this->buildCasts($module),

            'softDeletesImport' => '',

            'softDeletesTrait' => '',

            'constants' => '',

            'relationships' => '',

            'scopes' => '',
        ];
    }

    private function buildFillable(ModuleData $module): string
    {
        return '';
    }

    private function buildCasts(ModuleData $module): string
    {
        return '';
    }


}
