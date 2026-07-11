<?php

declare(strict_types=1);

namespace App\Core\Actions\Currency;


use App\Core\Actions\BaseAction;
use App\Core\Contracts\CurrencyServiceInterface;
use App\Models\Currency;

class CreateCurrencyAction extends BaseAction
{
    public function __construct(
        protected CurrencyServiceInterface $service
    ) {
    }

    /**
     * Crea una nueva moneda.
     */
    public function execute(
        array $attributes
    ): Currency {
        return $this->transaction(
            fn (): Currency => $this->service->create($attributes)
        );
    }
}
