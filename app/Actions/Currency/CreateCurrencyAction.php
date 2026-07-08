<?php

declare(strict_types=1);

namespace App\Actions\Currency;

use App\Core\Actions\BaseAction;
use App\Models\Currency;
use App\Services\CurrencyService;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Acción para crear una moneda.
 *
 * @package App\Actions\Currency
 * @since 1.0.0
 */
class CreateCurrencyAction extends BaseAction
{
    public function __construct(
        private readonly CurrencyService $service,
    ) {
    }

    public function __invoke(array $data): Currency
    {
        return $this->service->create($data);
    }
}
