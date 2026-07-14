<?php

declare(strict_types=1);

namespace App\Core\Generator\Pipeline;

use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\Results\GeneratorResult;
use App\Core\Generator\Contracts\PipelineStepInterface;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Motor de ejecución por etapas del CN Generator.
 *
 * Coordina la ejecución ordenada de pasos registrados,
 * permitiendo construir módulos mediante un flujo configurable.
 *
 * @package App\Core\Generator\Pipeline
 * @since 1.0.0
 */
final class GeneratorPipeline
{
    /**
     * @param iterable<PipelineStepInterface> $steps
     */
    public function __construct(
        private readonly iterable $steps,
    ) {
    }


    /**
     * Ejecuta todas las etapas del pipeline.
     */
    public function run(
        ModuleData $module
    ): GeneratorResult {

        $result = new GeneratorResult();


        foreach ($this->steps as $step) {

            $result->merge(
                $step->execute($module)
            );
        }


        return $result;
    }
}
