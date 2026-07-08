<?php

declare(strict_types=1);

namespace App\Actions\Currency;

use App\Models\Currency;
use App\Services\CurrencyService;
//use App\Models\Currency;

class DeleteCurrencyAction
{
    public function __construct(
        protected CurrencyService $service
    ) {
    }

    public function __invoke(Currency $currency): bool
    {
        return $this->service->delete($currency);
    }
}
