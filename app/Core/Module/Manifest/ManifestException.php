<?php

declare(strict_types=1);

namespace App\Core\Module\Manifest;

use RuntimeException;

/**
 * Excepción base del subsistema Module Manifest.
 *
 * Todas las operaciones relacionadas con lectura,
 * escritura, validación y construcción de manifiestos
 * deben lanzar esta excepción cuando ocurra un error
 * de dominio.
 */
final class ManifestException extends RuntimeException
{
    /**
     * El manifiesto no contiene un campo requerido.
     */
    public static function missingField(string $field): self
    {
        return new self(
            sprintf(
                'The manifest field "%s" is required.',
                $field
            )
        );
    }

    /**
     * El valor de un campo es inválido.
     */
    public static function invalidField(
        string $field,
        mixed $value
    ): self {
        return new self(
            sprintf(
                'Invalid value for manifest field "%s": %s.',
                $field,
                is_scalar($value)
                    ? (string) $value
                    : gettype($value)
            )
        );
    }

    /**
     * El manifiesto no pudo ser leído.
     */
    public static function unreadable(string $path): self
    {
        return new self(
            sprintf(
                'Unable to read manifest: %s.',
                $path
            )
        );
    }

    /**
     * El manifiesto no pudo ser escrito.
     */
    public static function unwritable(string $path): self
    {
        return new self(
            sprintf(
                'Unable to write manifest: %s.',
                $path
            )
        );
    }

    /**
     * El contenido JSON es inválido.
     */
    public static function invalidJson(): self
    {
        return new self(
            'The manifest contains invalid JSON.'
        );
    }
}
