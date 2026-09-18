<?php

declare(strict_types=1);

namespace App\Modules\State\Repositories;

use App\Modules\State\Models\State;
use App\Modules\State\Domain\Contracts\StateRepositoryInterface;
use App\Core\Repositories\BaseRepository;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Repositorio de State.
 *
 * Extiende el repositorio base del Core y utiliza el contrato
 * específico del módulo.
 *
 * @package App\Modules\State\Repositories
 */
class StateRepository
    extends BaseRepository
    implements StateRepositoryInterface
{
    /**
     * Constructor.
     */
    public function __construct(
        State $model,
    ) {
        parent::__construct($model);
    }
}
