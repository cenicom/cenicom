<?php

declare(strict_types=1);

namespace App\Core\Module\Bootstrap;

use App\Core\Contracts\Module\ModuleBootstrapPipelineInterface;
use App\Core\Contracts\Module\ModuleBootstrapStageInterface;
use Closure;

final class ModuleBootstrapPipeline implements ModuleBootstrapPipelineInterface
{
    /**
     * @param iterable<ModuleBootstrapStageInterface> $stages
     */
    public function __construct(
        private readonly iterable $stages,
        private readonly ?Closure $afterStage = null,
    ) {}

    public function process(ModuleBootstrapContext $context): void
    {
        foreach ($this->stages as $stage) {

            if (
                $context->hasException()
                || $context->isSkipped()
            ) {
                break;
            }

            $stage->process($context);

            if ($this->afterStage !== null) {
                ($this->afterStage)($stage, $context);
            }
        }
    }
}
