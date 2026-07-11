<?php

declare(strict_types=1);

namespace App\Core\Actions\Currency;

use App\Contracts\CurrencyServiceInterface;
use App\Core\Actions\BaseAction;
use App\Models\Currency;

class DeleteCurrencyAction extends BaseAction
{
    public function __construct(
        protected CurrencyServiceInterface $service
    ) {
    }

    /**
     * Elimina una moneda.
     */
    public function execute(Currency $currency): bool
    {
        return $this->transaction(
            fn (): bool => $this->service->delete($currency->getKey())
        );
    }
}
