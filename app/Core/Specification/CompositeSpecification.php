<?php

declare(strict_types=1);

namespace App\Core\Specification;

use App\Core\Specification\Contracts\SpecificationInterface;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Clase base para Specifications compuestas.
 *
 * Centraliza el contrato común para las Specifications
 * que combinan una o más reglas de negocio.
 *
 * Responsabilidades:
 *
 * - Implementar SpecificationInterface.
 * - Servir como base para composiciones.
 * - Mantener una API uniforme.
 *
 * No debe:
 *
 * - Implementar reglas concretas.
 * - Conocer entidades específicas.
 * - Conocer persistencia.
 *
 * ==========================================================
 */
abstract class CompositeSpecification implements SpecificationInterface
{
    // Aquí se pueden definir métodos comunes para las Specifications compuestas, si es necesario.

}
