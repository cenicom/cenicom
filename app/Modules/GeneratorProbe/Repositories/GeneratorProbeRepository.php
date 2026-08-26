<?php

declare(strict_types=1);

namespace App\Modules\GeneratorProbe\Repositories;

use App\Models\GeneratorProbe;
use App\Modules\GeneratorProbe\Domain\Contracts\GeneratorProbeRepositoryInterface;
use App\Core\Repositories\BaseRepository;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Repositorio de GeneratorProbe.
 *
 * Extiende el repositorio base del Core y utiliza el contrato
 * específico del módulo.
 *
 * @package App\Modules\GeneratorProbe\Repositories
 */
class GeneratorProbeRepository
    extends BaseRepository
    implements GeneratorProbeRepositoryInterface
{
    /**
     * Constructor.
     */
    public function __construct(
        GeneratorProbe $model,
    ) {
        parent::__construct($model);
    }
}
