<?php

declare(strict_types=1);

namespace App\Modules\Institution\Domain\Contracts;

use App\Core\Contracts\RepositoryInterface;
use App\Modules\Institution\Domain\Entity\Institution;

interface InstitutionRepositoryInterface extends RepositoryInterface
{
    public function save(Institution $institution): Institution;

    /**
     * Obtiene la institución bloqueando su fila.
     *
     * Requiere una transacción activa para conservar el bloqueo
     * durante la operación de ciclo de vida.
     */
    public function lockForUpdate(string $id): ?Institution;

    /**
     * Cambia el estado únicamente si coincide con el estado esperado.
     */
    public function transitionStatus(
        string $id,
        string $expectedStatus,
        string $targetStatus,
    ): ?Institution;
}
