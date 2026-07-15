<?php

declare(strict_types=1);

namespace App\Core\Generator\Results;

/**
 * Representa el resultado de una ejecución del CN Generator.
 *
 * Esta clase centraliza la información generada durante el proceso,
 * permitiendo registrar archivos creados, actualizados, omitidos,
 * advertencias y errores de forma consistente.
 *
 * Puede consolidar los resultados de múltiples generadores mediante
 * el método merge().
 */
final class GeneratorResult
{
    /**
     * @var array<string>
     */
    private array $created = [];

    /**
     * @var array<string>
     */
    private array $updated = [];

    /**
     * @var array<string>
     */
    private array $skipped = [];

    /**
     * @var array<string>
     */
    private array $warnings = [];

    /**
     * @var array<string>
     */
    private array $errors = [];

    public static function success(string $file): self
    {
        return (new self())->addCreated($file);
    }

    public static function failure(string $message): self
    {
        return (new self())->addError($message);
    }

    /**
     * Registra un archivo creado.
     */
    public function addCreated(string $file): self
    {
        $this->created[] = $file;

        return $this;
    }

    /**
     * Registra un archivo actualizado.
     */
    public function addUpdated(string $file): self
    {
        $this->updated[] = $file;

        return $this;
    }

    /**
     * Registra un archivo omitido.
     */
    public function addSkipped(string $file): self
    {
        $this->skipped[] = $file;

        return $this;
    }

    /**
     * Registra una advertencia.
     */
    public function addWarning(string $message): self
    {
        $this->warnings[] = $message;

        return $this;
    }

    /**
     * Registra un error.
     */
    public function addError(string $message): self
    {
        $this->errors[] = $message;

        return $this;
    }

    /**
     * Fusiona otro resultado con el actual.
     */
    public function merge(self $result): self
    {
        $this->created = [...$this->created, ...$result->created];
        $this->updated = [...$this->updated, ...$result->updated];
        $this->skipped = [...$this->skipped, ...$result->skipped];
        $this->warnings = [...$this->warnings, ...$result->warnings];
        $this->errors = [...$this->errors, ...$result->errors];

        return $this;
    }

    /**
     * Indica si la generación fue exitosa.
     */
    public function isSuccessful(): bool
    {
        return $this->errors === [];
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
     * @return array<string>
     */
    public function created(): array
    {
        return $this->created;
    }

    /**
     * @return array<string>
     */
    public function updated(): array
    {
        return $this->updated;
    }

    /**
     * @return array<string>
     */
    public function skipped(): array
    {
        return $this->skipped;
    }

    /**
     * @return array<string>
     */
    public function warnings(): array
    {
        return $this->warnings;
    }

    /**
     * @return array<string>
     */
    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * Devuelve un resumen estadístico.
     *
     * @return array<string, int>
     */
    public function summary(): array
    {
        return [
            'created' => count($this->created),
            'updated' => count($this->updated),
            'skipped' => count($this->skipped),
            'warnings' => count($this->warnings),
            'errors' => count($this->errors),
        ];
    }

    public function isEmpty(): bool
    {
        return $this->summary() === [
            'created' => 0,
            'updated' => 0,
            'skipped' => 0,
            'warnings' => 0,
            'errors' => 0,
        ];
    }

    public function totalGenerated(): int
    {
        return count($this->created)
            + count($this->updated);
    }
}
