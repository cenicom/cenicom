<?php

declare(strict_types=1);

namespace App\Modules\CnGeneratorProbe\Repositories;

use App\Modules\CnGeneratorProbe\Models\CnGeneratorProbe;
use App\Modules\CnGeneratorProbe\Domain\Contracts\CnGeneratorProbeRepositoryInterface;
use App\Core\Repositories\BaseRepository;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Repositorio de CnGeneratorProbe.
 *
 * Extiende el repositorio base del Core y utiliza el contrato
 * específico del módulo.
 *
 * @package App\Modules\CnGeneratorProbe\Repositories
 */
class CnGeneratorProbeRepository
    extends BaseRepository
    implements CnGeneratorProbeRepositoryInterface
{
    /**
     * Constructor.
     */
    public function __construct(
        CnGeneratorProbe $model,
    ) {
        parent::__construct($model);
    }
}
