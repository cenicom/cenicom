<?php

declare(strict_types=1);

namespace App\Core\Generator\Pipeline;

use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\Pipeline\Contracts\PipelineStepInterface;
use App\Core\Generator\Results\GeneratorResult;
use App\Core\Navigation\Contracts\NavigationRegistrarInterface;
use Closure;

/**
 * Registra la navegación del módulo dentro del NavigationRegistry.
 */
final readonly class RegisterNavigationStep implements PipelineStepInterface
{
    public function __construct(
        private NavigationRegistrarInterface $registrar,
    ) {
    }

    /**
     * Registra grupos e ítems de navegación y continúa el pipeline.
     */
    public function handle(
        ModuleData $module,
        GeneratorResult $result,
        Closure $next,
    ): GeneratorResult {

        $manifest = $module->navigation();

        if ($manifest->isEmpty()) {
            return $next($module, $result);
        }

        foreach ($manifest->groups as $group) {
            $this->registrar->group($group);
        }

        foreach ($manifest->items as $item) {
            $this->registrar->item($item);
        }

        return $next(
            $module,
            $result,
        );
    }
}
