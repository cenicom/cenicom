<?php

declare(strict_types=1);

namespace App\Core\Generator\Pipeline;

use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\Contracts\GeneratorManagerInterface;
use App\Core\Generator\Pipeline\Contracts\PipelineStepInterface;
use App\Core\Generator\Results\GeneratorResult;
use Closure;

/**
 * Ejecuta todos los generadores registrados mediante GeneratorManager.
 */
final readonly class ExecuteGeneratorsStep implements PipelineStepInterface
{
    public function __construct(
        private GeneratorManagerInterface $manager,
    ) {}

    /**
     * Ejecuta GeneratorManager y continúa el pipeline únicamente si
     * la generación fue exitosa.
     */
    public function handle(
        ModuleData $module,
        GeneratorResult $result,
        Closure $next,
    ): GeneratorResult {

        $generationResult = $this->manager->generate($module);

        if ($generationResult->hasErrors()) {
            return $generationResult;
        }

        return $next(
            $module,
            $generationResult,
        );
    }
}
