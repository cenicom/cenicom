<?php

declare(strict_types=1);

namespace App\Core\Module\Bootstrap;

use App\Core\Contracts\Module\ModuleManifestFinderInterface;
use App\Core\Contracts\Module\ModuleProviderRegistrarInterface;
use App\Core\Contracts\Module\ModuleRegistryInterface;
use App\Core\Module\Factory\ModuleDefinitionFactory;

final class ModuleBootstrap
{
    public function __construct(
        private readonly ModuleProviderRegistrarInterface $registrar,
        private readonly ModuleManifestFinderInterface $manifestFinder,
        private readonly ModuleRegistryInterface $registry,
        private readonly ModuleDefinitionFactory $factory,
    ) {}

    public function bootstrap(): void
    {
        //
        $manifests = $this->manifestFinder->find();

        foreach ($manifests as $manifestPath) {

            $definition = $this->factory->create($manifestPath);

            if (! $definition->enabled) {
                continue;
            }

            $this->registrar->registerDefinition($definition);

            $this->registry->register($definition);
        }
    }
}
