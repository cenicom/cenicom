<?php

declare(strict_types=1);

namespace App\Core\Module\Bootstrap\DTO;

use App\Core\Module\Bootstrap\Enums\ProviderStatus;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * DTO inmutable que representa el resultado del
 * registro de un Service Provider durante el
 * proceso de Bootstrapping.
 *
 * ERP-INT-004.3.5
 *
 * @author CENICOM
 */
final readonly class ProviderRegistrationResult
{
    /**
     * @param array<int, string> $errors
     */
    public function __construct(
        public string $module,
        public string $provider,
        public ProviderStatus $status,
        public array $errors = [],
        public float $executionTime = 0.0,
    ) {
    }
}
