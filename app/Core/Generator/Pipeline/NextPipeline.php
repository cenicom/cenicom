<?php

declare(strict_types=1);

namespace App\Core\Generator\Pipeline;

use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\Pipeline\Contracts\PipelineStepInterface;
use App\Core\Generator\Results\GeneratorResult;
use Closure;

final readonly class NextPipeline
{
    public function __construct(
        private PipelineStepInterface $step,
        private Closure $next,
    ) {
    }

    public function __invoke(
        ModuleData $module,
        GeneratorResult $result,
    ): GeneratorResult {
        return $this->handle($module, $result);
    }

    public function handle(
        ModuleData $module,
        GeneratorResult $result,
    ): GeneratorResult {

        return $this->step->handle(
            $module,
            $result,
            $this->next,
        );
    }
}
