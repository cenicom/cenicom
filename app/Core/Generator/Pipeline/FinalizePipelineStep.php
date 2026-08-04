<?php

declare(strict_types=1);

namespace App\Core\Generator\Pipeline;

use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\Pipeline\Contracts\PipelineStepInterface;
use App\Core\Generator\Results\GeneratorResult;
use Closure;

/**
 * Último paso del Pipeline del CN Generator.
 *
 * No realiza ninguna transformación adicional.
 * Únicamente delega al cierre del pipeline.
 */
final readonly class FinalizePipelineStep implements PipelineStepInterface
{
    public function handle(
        ModuleData $module,
        GeneratorResult $result,
        Closure $next,
    ): GeneratorResult {
        return $next(
            $module,
            $result,
        );
    }
}
