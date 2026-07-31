<?php

declare(strict_types=1);

namespace App\Core\Module\Bootstrap;

use App\Core\Contracts\Module\ModuleProviderRegistrarInterface;
use App\Core\Module\Bootstrap\Contracts\ModuleProviderValidatorInterface;
use App\Core\Module\DTO\ModuleDefinition;
use Illuminate\Contracts\Foundation\Application;


/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Registra dinámicamente los Service Providers
 * de todos los módulos instalados.
 *
 * Responsabilidades:
 *
 * - Descubrir módulos.
 * - Resolver Providers.
 * - Registrar Providers.
 * - Evitar registros duplicados.
 *
 * @package App\Core\Module\Bootstrap
 */
final class ModuleProviderRegistrar implements ModuleProviderRegistrarInterface
{
    public array $registered = [];

    public function __construct(
        private readonly ModuleProviderValidatorInterface $validator,
        private readonly Application $app,
    ) {}

    public function registerDefinition(
        ModuleDefinition $definition
    ): void {
        foreach ($definition->providers as $provider) {
            $this->registerProvider($provider);
        }
    }

    public function registerProvider(string $provider): void
    {
        if (isset($this->registered[$provider])) {
            return;
        }

        if (! $this->validator->validate($provider)) {
            return;
        }

        $this->app->register($provider);

        $this->registered[$provider] = true;
    }

    public function isRegistered(string $provider): bool
    {
        return isset($this->registered[$provider]);
    }
}
