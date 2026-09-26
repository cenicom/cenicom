<?php

declare(strict_types=1);

namespace App\Core\Activity\Contracts;

use App\Core\Activity\DTO\ActivityReadModel;

interface RecentActivityReaderInterface
{
    /**
     * Obtiene las actividades de negocio más recientes.
     *
     * El lector debe:
     * - devolver como máximo $limit elementos;
     * - ordenar por occurredAt descendente;
     * - incluir únicamente actividades de negocio;
     * - no generar HTML ni datos específicos de presentación.
     *
     * @return iterable<int, ActivityReadModel>
     *
     * @throws \InvalidArgumentException
     *         cuando $limit no es positivo.
     */
    public function recent(int $limit): iterable;
}
