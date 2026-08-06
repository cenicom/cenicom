<?php

declare(strict_types=1);

namespace App\Core\Module\Discovery;

use RuntimeException;
use Throwable;

/**
 * Excepción base del subsistema Module Discovery.
 *
 * Representa cualquier error producido durante el proceso
 * de descubrimiento, localización o carga inicial de módulos.
 *
 * Todas las excepciones específicas del Discovery deberán
 * derivarse de esta clase.
 */
final class DiscoveryException extends RuntimeException
{
    /**
     * Crea una nueva excepción de Discovery.
     */
    public function __construct(
        string $message,
        int $code = 0,
        ?Throwable $previous = null,
    ) {
        parent::__construct(
            $message,
            $code,
            $previous
        );
    }

    /**
     * El directorio de módulos no existe.
     */
    public static function directoryNotFound(
        string $directory
    ): self {
        return new self(
            sprintf(
                'Module directory [%s] was not found.',
                $directory
            )
        );
    }

    /**
     * No fue posible leer el directorio.
     */
    public static function directoryNotReadable(
        string $directory
    ): self {
        return new self(
            sprintf(
                'Module directory [%s] is not readable.',
                $directory
            )
        );
    }

    /**
     * No se encontraron módulos.
     */
    public static function noModulesFound(string $directory): self
    {
        return new self(
            sprintf(
                'No modules were found in [%s].',
                $directory
            )
        );
    }

    /**
     * Archivo de manifiesto inexistente.
     */
    public static function manifestNotFound(
        string $path
    ): self {
        return new self(
            sprintf(
                'Manifest [%s] was not found.',
                $path
            )
        );
    }

    /**
     * Error genérico durante el descubrimiento.
     */
    public static function discoveryFailed(
        string $reason
    ): self {
        return new self(
            sprintf(
                'Module discovery failed: %s',
                $reason
            )
        );
    }
}
