<?php

declare(strict_types=1);

namespace App\Core\Generator\Contracts;

use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\Results\GeneratorResult;

interface PipelineStepInterface
{
    /**
     * Ejecuta una etapa del pipeline.
     */
    public function execute(
        ModuleData $module
    ): GeneratorResult;
}
