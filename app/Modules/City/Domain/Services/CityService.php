<?php

declare(strict_types=1);

namespace App\Modules\City\Domain\Services;

use App\Modules\City\Domain\Contracts\CityRepositoryInterface;
use App\Modules\City\Domain\Contracts\CityServiceInterface;
use App\Core\Services\BaseService;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Servicio del módulo City.
 *
 * Extiende el servicio base del Core y utiliza el contrato
 * específico del módulo.
 *
 * @package App\Modules\City\Domain\Services
 */
class CityService
    extends BaseService
    implements CityServiceInterface
{
    /**
     * Constructor.
     */
    public function __construct(
        CityRepositoryInterface $repository,
    ) {
        parent::__construct($repository);
    }
}
