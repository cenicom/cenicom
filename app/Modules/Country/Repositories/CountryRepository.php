<?php

declare(strict_types=1);

namespace App\Modules\Country\Repositories;

use App\Modules\Country\Models\Country;
use App\Modules\Country\Domain\Contracts\CountryRepositoryInterface;
use App\Core\Repositories\BaseRepository;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Repositorio de Country.
 *
 * Extiende el repositorio base del Core y utiliza el contrato
 * específico del módulo.
 *
 * @package App\Modules\Country\Repositories
 */
class CountryRepository
    extends BaseRepository
    implements CountryRepositoryInterface
{
    /**
     * Constructor.
     */
    public function __construct(
        Country $model,
    ) {
        parent::__construct($model);
    }
}
