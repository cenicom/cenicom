<?php

declare(strict_types=1);

namespace App\Core\Module\Bootstrap\Contracts;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Contrato para validar un Service Provider.
 *
 * ERP-INT-004.3.5
 *
 * @author CENICOM
 */
interface ModuleProviderValidatorInterface
{
    /**
     * Determina si el Service Provider
     * puede ser registrado.
     */
    public function validate(string $provider): bool;
}
