<?php

declare(strict_types=1);

namespace App\Core\Generator\Pipeline;

use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\Pipeline\Contracts\PipelineStepInterface;
use App\Core\Generator\Results\GeneratorResult;
use App\Core\Generator\Pipeline\Contracts\PipelineInterface;
use Closure;

final class Pipeline implements PipelineInterface
{
    /**
     * @param array<int, PipelineStepInterface> $steps
     */
    public function __construct(
        private array $steps = [],
    ) {
    }

    public function process(
        ModuleData $module,
    ): GeneratorResult {

        $pipeline = array_reduce(
            array_reverse($this->steps),
            function (Closure $next, PipelineStepInterface $step): Closure {
                return function (
                    ModuleData $module,
                    GeneratorResult $result,
                ) use ($step, $next): GeneratorResult {

                    return (new NextPipeline(
                        $step,
                        $next,
                    ))->handle(
                        $module,
                        $result,
                    );
                };
            },
            fn (
                ModuleData $module,
                GeneratorResult $result,
            ): GeneratorResult => $result,
        );

        return $pipeline(
            $module,
            new GeneratorResult(),
        );
    }
}
