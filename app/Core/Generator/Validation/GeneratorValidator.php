<?php

declare(strict_types=1);

namespace App\Core\Generator\Validation;


use App\Core\Generator\Specifications\ModuleSpecification;
use App\Core\Generator\Validation\Contracts\ValidatorInterface;
use App\Core\Generator\Validation\Exceptions\ValidationException;
use App\Core\Generator\Validation\Results\ValidationResult;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Orquesta la ejecución de todos los validadores del
 * CN Generator.
 *
 * Recorre la colección de validadores registrados y ejecuta
 * únicamente aquellos que implementan ValidatorInterface.
 *
 * Consolida los resultados de validación y aborta el proceso
 * cuando se detectan errores.
 *
 * Este componente no contiene reglas de negocio; únicamente
 * coordina el proceso de validación.
 *
 * @package App\Core\Generator\Validation
 * @since 1.0.0
 */
final class GeneratorValidator
{
    /**
     * @param iterable<ValidatorInterface> $validators
     */
    public function __construct(
        private readonly iterable $validators,
    ) {
    }

    /**
     * Ejecuta la validación completa del manifiesto.
     *
     * @throws ValidationException
     */
    public function validate(
        ModuleSpecification $specification,
    ): ValidationResult {
        $result = new ValidationResult();

        foreach ($this->validators as $validator) {

            $partial = $validator->validate($specification);

            $result->merge($partial);
        }

        if (! $result->isValid()) {
            throw ValidationException::fromResult($result);
        }

        return $result;
    }
}
