<?php

declare(strict_types=1);

namespace App\Core\Generator\Pipeline\Contracts;

use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\Results\GeneratorResult;
use Closure;

/**
 * Contrato base para todos los pasos del Generator Pipeline.
 */
interface PipelineStepInterface
{
    /**
     * Ejecuta el paso del pipeline.
     */
    public function handle(
        ModuleData $module,
        GeneratorResult $result,
        Closure $next,
    ): GeneratorResult;
}
