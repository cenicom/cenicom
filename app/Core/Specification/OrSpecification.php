<?php

declare(strict_types=1);

namespace App\Core\Specification;

use App\Core\Specification\CompositeSpecification;
use App\Core\Specification\Contracts\SpecificationInterface;



/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Specification que representa la operación lógica OR.
 *
 * Responsabilidades:
 *
 * - Componer múltiples Specifications.
 * - Evaluar si alguna Specification es satisfecha.
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
final class OrSpecification extends CompositeSpecification
{
    // aquí se incorpora la propiedad que almacenará las Specifications a combinar
    /**
     * Specifications que componen la operación lógica OR.
     *
     * @var list<SpecificationInterface>
     */
    private array $specifications;

    public function __construct(
        SpecificationInterface ...$specifications
    ) {
        $this->specifications = $specifications;
    }

    /**
     * Determina si una entidad satisface al menos una
     * de las Specifications que componen esta operación.
     */
    public function isSatisfiedBy(
        mixed $candidate
    ): bool {
        foreach ($this->specifications as $specification) {

            if ($specification->isSatisfiedBy($candidate)) {
                return true;
            }
        }

        return false;
    }
}
