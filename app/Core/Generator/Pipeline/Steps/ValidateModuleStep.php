<?php

declare(strict_types=1);

namespace App\Core\Generator\Pipeline\Steps;

use App\Core\Generator\Contracts\PipelineStepInterface;
use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\Results\GeneratorResult;

final readonly class ValidateModuleStep implements PipelineStepInterface
{
    public function execute(
        ModuleData $module
    ): GeneratorResult {

        $result = new GeneratorResult();


        if ($module->name() === '') {
            $result->addError(
                'Module name is required.'
            );
        }


        return $result;
    }
}
