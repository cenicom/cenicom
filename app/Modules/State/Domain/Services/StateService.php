<?php

declare(strict_types=1);

namespace App\Modules\State\Domain\Services;

use App\Modules\State\Domain\Contracts\StateRepositoryInterface;
use App\Modules\State\Domain\Contracts\StateServiceInterface;
use App\Core\Services\BaseService;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Servicio del módulo State.
 *
 * Extiende el servicio base del Core y utiliza el contrato
 * específico del módulo.
 *
 * @package App\Modules\State\Domain\Services
 */
class StateService
    extends BaseService
    implements StateServiceInterface
{
    /**
     * Constructor.
     */
    public function __construct(
        StateRepositoryInterface $repository,
    ) {
        parent::__construct($repository);
    }
}
