<?php

declare(strict_types=1);

namespace App\Modules\GeneratorProbe\Services;

use App\Modules\GeneratorProbe\Domain\Contracts\GeneratorProbeRepositoryInterface;
use App\Modules\GeneratorProbe\Domain\Contracts\GeneratorProbeServiceInterface;
use App\Core\Services\BaseService;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Servicio del módulo GeneratorProbe.
 *
 * Extiende el servicio base del Core y utiliza el contrato
 * específico del módulo.
 *
 * @package App\Modules\GeneratorProbe\Services
 */
class GeneratorProbeService
    extends BaseService
    implements GeneratorProbeServiceInterface
{
    /**
     * Constructor.
     */
    public function __construct(
        GeneratorProbeRepositoryInterface $repository,
    ) {
        parent::__construct($repository);
    }
}
