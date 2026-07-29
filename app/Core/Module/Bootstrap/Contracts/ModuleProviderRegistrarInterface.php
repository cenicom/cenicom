<?php

declare(strict_types=1);

namespace App\Core\Module\Bootstrap\Contracts;

use App\Core\Module\Bootstrap\DTO\ProviderRegistrationResult;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Contrato para registrar Service Providers
 * dentro del ciclo de vida de Laravel.
 *
 * ERP-INT-004.3.5
 *
 * @author CENICOM
 */
interface ModuleProviderRegistrarInterface
{
    /**
     * Registra el Service Provider indicado.
     */
    public function register(
        string $module,
        string $provider,
    ): ProviderRegistrationResult;
}
