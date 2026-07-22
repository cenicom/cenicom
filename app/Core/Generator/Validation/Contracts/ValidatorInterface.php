<?php

declare(strict_types=1);

namespace App\Core\Generator\Validation\Contracts;

use App\Core\Generator\Specifications\ModuleSpecification;
use App\Core\Generator\Validation\Results\ValidationResult;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Contrato base para validadores del CN Generator.
 *
 * Define la operación mínima que debe implementar
 * cualquier validador de estructura o datos.
 *
 * @package App\Core\Generator\Validation\Contracts
 * @since 1.0.0
 */
interface ValidatorInterface
{

    public function validate(
        ModuleSpecification $specification
    ): ValidationResult;
}
