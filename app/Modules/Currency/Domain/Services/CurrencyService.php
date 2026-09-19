<?php

declare(strict_types=1);

namespace App\Modules\Currency\Domain\Services;

use App\Modules\Currency\Domain\Contracts\CurrencyRepositoryInterface;
use App\Modules\Currency\Domain\Contracts\CurrencyServiceInterface;
use App\Core\Services\BaseService;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Servicio del módulo Currency.
 *
 * Extiende el servicio base del Core y utiliza el contrato
 * específico del módulo.
 *
 * @package App\Modules\Currency\Domain\Services
 */
class CurrencyService
    extends BaseService
    implements CurrencyServiceInterface
{
    /**
     * Constructor.
     */
    public function __construct(
        CurrencyRepositoryInterface $repository,
    ) {
        parent::__construct($repository);
    }
}
