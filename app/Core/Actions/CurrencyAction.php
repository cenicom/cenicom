<?php

declare(strict_types=1);

namespace App\Core\Actions;

use App\Models\Currency;
use App\Core\Contracts\CurrencyServiceInterface;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Acción CurrencyAction.
 *
 * Encapsula una operación específica del módulo.
 *
 * @package App\Core\Actions
 */
final readonly class CurrencyAction
{
    public function __construct(
        private CurrencyServiceInterface $service,
    ) {
    }

    /**
     * @param array<string,mixed> $data
     */
    public function create(
        array $data
    ): Currency
    {
        return $this->service->create($data);
    }

    /**
     * @param array<string,mixed> $data
     */
    public function update(
        Currency $currency,
        array $data
    ): bool
    {
        return $this->service->update(
            $currency,
            $data
        );
    }

    public function destroy(
        Currency $currency
    ): bool
    {
        return $this->service->destroy(
            $currency
        );
    }
}
