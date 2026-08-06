<?php

declare(strict_types=1);

namespace App\Core\Module\Providers;

use App\Core\Contracts\Module\ModuleProviderRegistrarInterface;
use App\Core\Module\DTO\ModuleDefinition;
use Illuminate\Contracts\Foundation\Application;

/**
 * Registers the service providers declared by a module definition.
 */
final class ModuleProviderRegistrar implements ModuleProviderRegistrarInterface
{
    public function __construct(
        private readonly Application $app,
    ) {
    }

    public function registerDefinition(ModuleDefinition $definition,): void
    {
        foreach (array_unique($definition->providers) as $provider) {
            $this->app->register($provider);
        }
    }
}
