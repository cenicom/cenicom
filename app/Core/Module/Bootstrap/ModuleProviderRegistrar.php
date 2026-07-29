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
    /**
     * Providers registrados.
     *
     * @var array<string, bool>
     */
    private array $registered = [];

    /**
     * Constructor.
     */
    public function __construct(
        // Dependencias (a incorporar durante la implementación)

        private readonly ModuleProviderValidatorInterface $validator,
        private readonly Application $app,
    ) {}

    /**
     * Registra todos los providers encontrados.
     */
    public function register(): void
    {
        //

    }


    /**
     * Registra todos los Service Providers de un módulo.
     */
    public function registerModule(string $module): void
    {
        $providers = $this->resolveProviders($module);

        foreach ($providers as $provider) {
            $this->registerProvider($provider);
        }
    }

    /**
     * Registra un Service Provider.
     */
    public function registerProvider(string $provider): void
    {
        if ($this->isRegistered($provider)) {
            return;
        }

        if (! $this->providerExists($provider)) {
            return;
        }

        $this->validator->validate($provider);

        $this->registerIntoApplication($provider);

        $this->markAsRegistered($provider);
    }

    /**
     * Verifica si un provider ya fue registrado.
     */
    public function isRegistered(string $provider): bool
    {
        return isset($this->registered[$provider]);
    }

    /**
     * Marca un provider como registrado.
     */
    private function markAsRegistered(string $provider): void
    {
        $this->registered[$provider] = true;
    }

    /**
     * Obtiene el listado de providers del módulo.
     *
     * @return array<int,string>
     */
    private function resolveProviders(string $module): array
    {
        return config("modules.{$module}.providers", []);
    }

    /**
     * Valida la existencia del provider.
     */
    private function providerExists(string $provider): bool
    {
        return class_exists($provider);
    }

    /**
     * Registra el provider dentro de Laravel.
     */
    private function registerIntoApplication(string $provider): void
    {
        //
        $this->app->register($provider);
    }

    public function registerDefinition(
        ModuleDefinition $definition
    ): void {
        foreach ($definition->providers as $provider) {

            $this->registerProvider($provider);
        }
    }
}
