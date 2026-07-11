<?php

declare(strict_types=1);

namespace App\Core\Actions\Currency;

use App\Contracts\CurrencyServiceInterface;
use App\Core\Actions\BaseAction;
use App\Models\Currency;

class UpdateCurrencyAction extends BaseAction
{
    public function __construct(
        protected CurrencyServiceInterface $service
    ) {
    }

    /**
     * Actualiza una moneda existente.
     */
    public function execute(
        Currency $currency,
        array $attributes
    ): bool {
        return $this->transaction(
            fn (): bool => $this->service->update(
                $currency->getKey(),
                $attributes
            )
        );
    }
}
