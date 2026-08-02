<?php

declare(strict_types=1);

namespace App\Core\Specification\Contracts;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Contrato base para todas las Specifications.
 *
 * Una Specification representa una regla de negocio
 * reutilizable que puede evaluar si una entidad
 * satisface un criterio determinado.
 *
 * Responsabilidades:
 *
 * - Definir el contrato común de evaluación.
 * - Ser independiente de la infraestructura.
 * - Permitir la composición de reglas.
 *
 * No debe:
 *
 * - Conocer Eloquent.
 * - Conocer SQL.
 * - Modificar entidades.
 * - Implementar lógica de composición.
 *
 * ==========================================================
 */
interface SpecificationInterface
{
    /**
     * Determina si una entidad satisface la Specification.
     */
    public function isSatisfiedBy(
        mixed $candidate
    ): bool;
}
