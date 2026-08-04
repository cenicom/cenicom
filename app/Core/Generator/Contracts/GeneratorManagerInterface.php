<?php

declare(strict_types=1);

namespace App\Core\Generator\Contracts;

use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\Results\GeneratorResult;

interface GeneratorManagerInterface
{
    public function generate(
        ModuleData $module,
    ): GeneratorResult;
}
