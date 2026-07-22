<?php

declare(strict_types=1);

namespace App\Core\Generator\Validation\Results;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Resultado de una validación del CN Generator.
 *
 * Almacena los errores, advertencias e información generados
 * durante la validación de un manifiesto.
 *
 * Puede combinar resultados provenientes de múltiples
 * validadores mediante merge().
 *
 * @package App\Core\Generator\Validation\Results
 * @since 1.0.0
 */
final class ValidationResult
{
    /**
     * @var array<string>
     */
    private array $errors = [];

    /**
     * @var array<string>
     */
    private array $warnings = [];

    /**
     * @var array<string>
     */
    private array $infos = [];

    /**
     * Agrega un error.
     */
    public function addError(string $message): void
    {
        $this->errors[] = $message;
    }

    /**
     * Agrega una advertencia.
     */
    public function addWarning(string $message): void
    {
        $this->warnings[] = $message;
    }

    /**
     * Agrega un mensaje informativo.
     */
    public function addInfo(string $message): void
    {
        $this->infos[] = $message;
    }

    /**
     * Combina otro resultado de validación.
     */
    public function merge(self $result): void
    {
        $this->errors = [
            ...$this->errors,
            ...$result->errors(),
        ];

        $this->warnings = [
            ...$this->warnings,
            ...$result->warnings(),
        ];

        $this->infos = [
            ...$this->infos,
            ...$result->infos(),
        ];
    }

    /**
     * Indica si existen errores.
     */
    public function hasErrors(): bool
    {
        return $this->errors !== [];
    }

    /**
     * Indica si existen advertencias.
     */
    public function hasWarnings(): bool
    {
        return $this->warnings !== [];
    }

    /**
     * Indica si la validación fue satisfactoria.
     */
    public function isValid(): bool
    {
        return ! $this->hasErrors();
    }

    /**
     * Devuelve los errores.
     *
     * @return array<string>
     */
    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * Devuelve las advertencias.
     *
     * @return array<string>
     */
    public function warnings(): array
    {
        return $this->warnings;
    }

    /**
     * Devuelve la información.
     *
     * @return array<string>
     */
    public function infos(): array
    {
        return $this->infos;
    }
}
