<?php

declare(strict_types=1);

namespace App\Core\Specification;

use App\Core\Specification\Contracts\SpecificationInterface;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Clase base para todas las Specifications atómicas.
 *
 * Responsabilidades:
 *
 * - Servir como base para Specifications simples.
 * - Implementar el contrato SpecificationInterface.
 * - Proporcionar un punto común de extensión.
 *
 * No debe:
 *
 * - Combinar Specifications.
 * - Acceder a infraestructura.
 * - Implementar reglas de negocio genéricas.
 *
 * ==========================================================
 */
abstract class AtomicSpecification
    implements SpecificationInterface
{

}
