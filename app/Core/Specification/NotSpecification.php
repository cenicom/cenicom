<?php

declare(strict_types=1);

namespace App\Core\Specification;

use App\Core\Specification\Contracts\SpecificationInterface;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Specification que representa la operación lógica NOT.
 *
 * Responsabilidades:
 *
 * - Negar el resultado de una Specification.
 * - Invertir el resultado lógico de la evaluación.
 * - Servir como operador lógico reutilizable.
 *
 * No debe:
 *
 * - Conocer entidades concretas.
 * - Acceder a infraestructura.
 * - Consultar base de datos.
 * - Resolver reglas de negocio externas.
 *
 * ==========================================================
 */
final class NotSpecification extends CompositeSpecification
{
    // aquí se incorpora la propiedad
    /**
     * Specification que será negada.
     */
    private SpecificationInterface $specification;

    public function __construct(SpecificationInterface $specification)
    {
        $this->specification = $specification;
    }

    /**
     * Determina si una entidad NO satisface
     * la Specification interna.
     */
    public function isSatisfiedBy(mixed $candidate): bool
    {
        return ! $this->specification->isSatisfiedBy($candidate);
    }
}
