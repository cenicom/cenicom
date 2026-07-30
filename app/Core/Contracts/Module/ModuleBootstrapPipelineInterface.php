<?php

declare(strict_types=1);

namespace App\Core\Contracts\Module;

use App\Core\Module\Bootstrap\ModuleBootstrapContext;

/**
 * Coordinates the execution of the module bootstrap pipeline.
 *
 * The pipeline orchestrates the execution of all configured stages
 * using a shared ModuleBootstrapContext.
 */
interface ModuleBootstrapPipelineInterface
{
    /**
     * Executes the configured bootstrap stages.
     */
    public function process(ModuleBootstrapContext $context): void;
}
