<?php

declare(strict_types=1);

namespace App\Modules\Campus\Domain\Contracts;

use App\Core\Contracts\RepositoryInterface;
use App\Modules\Campus\Domain\Entity\Campus;

interface CampusRepositoryInterface extends RepositoryInterface
{
    public function save(Campus $campus): Campus;

    /**
     * Obtiene la sede bloqueando su fila.
     *
     * Requiere una transacción activa.
     */
    public function lockForUpdate(string $id): ?Campus;

    /**
     * Cambia el estado únicamente si coincide con el estado esperado.
     */
    public function transitionStatus(
        string $id,
        string $expectedStatus,
        string $targetStatus,
    ): ?Campus;
}
