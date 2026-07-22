<?php

declare(strict_types=1);

namespace App\Core\Generator\Validation\Validators;


use App\Core\Generator\Specifications\ModuleSpecification;
use App\Core\Generator\Validation\Contracts\ValidatorInterface;
use App\Core\Generator\Validation\Results\ValidationResult;


/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Valida la identidad del módulo definida en el manifiesto.
 *
 * Comprueba que las propiedades obligatorias del bloque
 * "identity" existan y contengan valores válidos antes de
 * permitir la generación del módulo.
 *
 * Este validador únicamente verifica la identidad del módulo.
 * No valida campos, relaciones, navegación ni permisos.
 *
 * @package App\Core\Generator\Validation\Validators
 * @since 1.0.0
 */
final class IdentityValidator implements ValidatorInterface
{
    /**
     * Propiedades obligatorias del bloque identity.
     *
     * @var array<string>
     */
    private const REQUIRED = [
        'name',
        'singular',
        'plural',
        'table',
        'description',
    ];

    /**
     * Ejecuta la validación.
     */
    public function validate(ModuleSpecification $specification): ValidationResult
    {
        $result = new ValidationResult();

        foreach (self::REQUIRED as $property) {
            $value = $specification->{$property}() ?? null;

            if (! is_string($value) || trim($value) === '') {
                $result->addError(
                    sprintf(
                        'Identity: "%s" is required.',
                        $property
                    )
                );
            }
        }

        return $result;
    }
}