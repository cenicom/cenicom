<?php

declare(strict_types=1);

namespace App\Core\Specification;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Specification que siempre retorna verdadero.
 *
 * Utilizada como Specification de referencia
 * para validar la infraestructura del patrón.
 *
 * ==========================================================
 */
final class AlwaysTrueSpecification
    extends AtomicSpecification
{
    /**
     * Determina si una entidad satisface
     * la Specification.
     */
    public function isSatisfiedBy(mixed $candidate): bool
    {
        return true;
    }
}
