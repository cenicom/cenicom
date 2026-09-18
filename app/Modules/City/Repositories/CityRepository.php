<?php

declare(strict_types=1);

namespace App\Modules\City\Repositories;

use App\Modules\City\Models\City;
use App\Modules\City\Domain\Contracts\CityRepositoryInterface;
use App\Core\Repositories\BaseRepository;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Repositorio de City.
 *
 * Extiende el repositorio base del Core y utiliza el contrato
 * específico del módulo.
 *
 * @package App\Modules\City\Repositories
 */
class CityRepository
    extends BaseRepository
    implements CityRepositoryInterface
{
    /**
     * Constructor.
     */
    public function __construct(
        City $model,
    ) {
        parent::__construct($model);
    }
}
