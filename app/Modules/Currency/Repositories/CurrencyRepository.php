<?php

declare(strict_types=1);

namespace App\Modules\Currency\Repositories;

use App\Modules\Currency\Models\Currency;
use App\Modules\Currency\Domain\Contracts\CurrencyRepositoryInterface;
use App\Core\Repositories\BaseRepository;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Repositorio de Currency.
 *
 * Extiende el repositorio base del Core y utiliza el contrato
 * específico del módulo.
 *
 * @package App\Modules\Currency\Repositories
 */
class CurrencyRepository
    extends BaseRepository
    implements CurrencyRepositoryInterface
{
    /**
     * Constructor.
     */
    public function __construct(
        Currency $model,
    ) {
        parent::__construct($model);
    }
}
