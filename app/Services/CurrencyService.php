<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Services\BaseService;
use App\Repositories\CurrencyRepository;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Servicio de monedas.
 *
 * @package App\Services
 * @since 1.0.0
 */
class CurrencyService extends BaseService
{
    public function __construct(
        CurrencyRepository $repository
    ) {
        parent::__construct($repository);
    }
}
