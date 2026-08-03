<?php

declare(strict_types=1);

namespace App\Core\Specification;

use App\Core\Specification\Contracts\SpecificationInterface;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Specification que representa la operación lógica AND.
 *
 * Una entidad satisface esta Specification únicamente
 * cuando todas las Specifications que la componen
 * son satisfechas.
 *
 * Responsabilidades:
 *
 * - Evaluar múltiples Specifications.
 * - Aplicar la operación lógica AND.
 * - Mantener una composición inmutable.
 *
 * No debe:
 *
 * - Conocer Eloquent.
 * - Conocer SQL.
 * - Modificar la entidad evaluada.
 * - Implementar otras operaciones lógicas.
 *
 * ==========================================================
 */
final class AndSpecification extends CompositeSpecification
{
    /**
     * Specifications que componen la operación lógica AND.
     *
     * @var array<int, SpecificationInterface>
     */
    private array $specifications;

    public function __construct(
        SpecificationInterface ...$specifications
    ) {
        $this->specifications = $specifications;
    }

    /**
     * Determina si una entidad satisface todas las
     * Specifications que componen esta operación.
     */
    public function isSatisfiedBy(
        mixed $candidate
    ): bool {

        foreach ($this->specifications as $specification) {

            if (! $specification->isSatisfiedBy($candidate)) {
                return false;
            }
        }

        return true;
    }
}
