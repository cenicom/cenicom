<?php

declare(strict_types=1);

namespace App\Core\Module\Bootstrap;

use App\Core\Module\Bootstrap\Contracts\ModuleProviderValidatorInterface;
use Illuminate\Support\ServiceProvider;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Responsable de validar que un Service Provider
 * pueda formar parte del proceso de Bootstrapping
 * Modular.
 *
 * Este componente:
 *
 *  - Verifica la existencia de la clase.
 *  - Comprueba que extienda ServiceProvider.
 *
 * No registra.
 * No instancia.
 * No ejecuta código del Provider.
 *
 * ERP-INT-004.3.5
 *
 * @author CENICOM
 */
final readonly class ModuleProviderValidator implements ModuleProviderValidatorInterface
{
    /**
     * {@inheritDoc}
     */
    public function validate(string $provider): bool
    {
        if (! $this->providerExists($provider)) {
            return false;
        }

        return $this->isServiceProvider($provider);
    }

    /**
     * Determina si la clase existe.
     */
    private function providerExists(
        string $provider,
    ): bool {
        return class_exists($provider);
    }

    /**
     * Determina si la clase hereda
     * de Illuminate\Support\ServiceProvider.
     */
    private function isServiceProvider(
        string $provider,
    ): bool {
        return is_subclass_of(
            $provider,
            ServiceProvider::class,
        );
    }
}
