<?php

declare(strict_types=1);

namespace App\Actions\Currency;

use App\Models\Currency;
use App\Services\CurrencyService;

class UpdateCurrencyAction
{
    public function __construct(
        protected CurrencyService $service
    ) {
    }

    public function __invoke(Currency $currency, array $data): Currency
{
    return $this->service->update($currency, $data);
}
}
