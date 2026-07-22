<?php

declare(strict_types=1);

namespace App\Core\Generator\Validation\Exceptions;

use App\Core\Generator\Validation\Results\ValidationResult;
use RuntimeException;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Excepción lanzada cuando el manifiesto de un módulo no
 * supera el proceso de validación.
 *
 * Encapsula el ValidationResult para que el comando Artisan
 * pueda mostrar un reporte completo de los errores
 * encontrados antes de abortar la generación.
 *
 * @package App\Core\Generator\Validation\Exceptions
 * @since 1.0.0
 */
final class ValidationException extends RuntimeException
{
    /**
     * Resultado de la validación.
     */
    public function __construct(
        private readonly ValidationResult $result,
        string $message = 'Module validation failed.',
    ) {
        parent::__construct($message);
    }

    /**
     * Devuelve el resultado completo de la validación.
     */
    public function result(): ValidationResult
    {
        return $this->result;
    }

    /**
     * Crea una excepción a partir de un ValidationResult.
     */
    public static function fromResult(ValidationResult $result): self
    {
        return new self($result);
    }


}
