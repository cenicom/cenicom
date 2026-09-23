<?php

declare(strict_types=1);

namespace App\Modules\CnGeneratorProbe\Domain\Services;

use App\Modules\CnGeneratorProbe\Domain\Contracts\CnGeneratorProbeRepositoryInterface;
use App\Modules\CnGeneratorProbe\Domain\Contracts\CnGeneratorProbeServiceInterface;
use App\Core\Services\BaseService;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Servicio del módulo CnGeneratorProbe.
 *
 * Extiende el servicio base del Core y utiliza el contrato
 * específico del módulo.
 *
 * @package App\Modules\CnGeneratorProbe\Domain\Services
 */
class CnGeneratorProbeService
    extends BaseService
    implements CnGeneratorProbeServiceInterface
{
    /**
     * Constructor.
     */
    public function __construct(
        CnGeneratorProbeRepositoryInterface $repository,
    ) {
        parent::__construct($repository);
    }
}
