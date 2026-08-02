<?php

declare(strict_types=1);

namespace App\Core\Specification;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Specification que siempre retorna falso.
 *
 * Utilizada como Specification de referencia
 * para validar la infraestructura del patrón.
 *
 * ==========================================================
 */
final class AlwaysFalseSpecification
    extends AtomicSpecification
{
    /**
     * Nunca considera satisfecho al candidato.
     */
    public function isSatisfiedBy(
        mixed $candidate
    ): bool {
        return false;
    }
}
