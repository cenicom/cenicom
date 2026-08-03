<?php

declare(strict_types=1);

namespace App\Core\Generator\Pipeline\Steps;

use App\Core\Generator\Contracts\PipelineStepInterface;

final readonly class ExecuteGeneratorsStep
    implements PipelineStepInterface
{
    public function __construct(
        private GeneratorManager $manager,
    ) {
    }


    public function execute(
        ModuleData $module
    ): GeneratorResult {

        return $this->manager->execute($module);
    }
}
