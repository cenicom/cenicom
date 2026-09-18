<?php

declare(strict_types=1);

namespace App\Modules\Country\Domain\Services;

use App\Modules\Country\Domain\Contracts\CountryRepositoryInterface;
use App\Modules\Country\Domain\Contracts\CountryServiceInterface;
use App\Core\Services\BaseService;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Servicio del módulo Country.
 *
 * Extiende el servicio base del Core y utiliza el contrato
 * específico del módulo.
 *
 * @package App\Modules\Country\Domain\Services
 */
class CountryService
    extends BaseService
    implements CountryServiceInterface
{
    /**
     * Constructor.
     */
    public function __construct(
        CountryRepositoryInterface $repository,
    ) {
        parent::__construct($repository);
    }
}
