<?php

declare(strict_types=1);

namespace App\Core\Contracts\Module;

use App\Core\Module\Bootstrap\ModuleBootstrapContext;

/**
 * Represents a single stage of the module bootstrap pipeline.
 *
 * Each implementation must perform exactly one responsibility while
 * reading and updating the shared ModuleBootstrapContext.
 */
interface ModuleBootstrapStageInterface
{
    /**
     * Executes the stage.
     */
    public function process(ModuleBootstrapContext $context): void;
}
