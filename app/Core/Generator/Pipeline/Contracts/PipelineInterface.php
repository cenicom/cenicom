<?php

declare(strict_types=1);

namespace App\Core\Generator\Pipeline\Contracts;

use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\Results\GeneratorResult;

interface PipelineInterface
{
    public function process(
        ModuleData $module,
    ): GeneratorResult;
}
