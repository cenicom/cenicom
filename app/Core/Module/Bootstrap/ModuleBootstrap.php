<?php

declare(strict_types=1);

namespace App\Core\Module\Bootstrap;

use App\Core\Contracts\Module\ModuleBootstrapPipelineInterface;
use App\Core\Contracts\Module\ModuleManifestFinderInterface;

final class ModuleBootstrap
{
    public function __construct(
        private readonly ModuleManifestFinderInterface $manifestFinder,
        private readonly ModuleBootstrapPipelineInterface $pipeline,
    ) {
    }

    /**
     * Bootstraps all discovered modules.
     */
    public function bootstrap(): void
    {
        foreach ($this->manifestFinder->find() as $manifestPath) {

            $context = new ModuleBootstrapContext(
                $manifestPath
            );

            $this->pipeline->process($context);
        }
    }
}
