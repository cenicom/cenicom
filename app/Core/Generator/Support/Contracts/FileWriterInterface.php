<?php

declare(strict_types=1);

namespace App\Core\Generator\Support\Contracts;

/**
 * Contrato para la escritura y gestión de archivos del CN Generator.
 *
 * Abstrae las operaciones sobre el sistema de archivos para
 * desacoplar la infraestructura de los pasos del pipeline
 * y facilitar las pruebas unitarias.
 */
interface FileWriterInterface
{
    /**
     * Escribe un archivo en disco.
     */
    public function write(
        string $path,
        string $contents,
        bool $overwrite = false,
    ): void;

    /**
     * Determina si un archivo existe.
     */
    public function exists(
        string $path,
    ): bool;

    /**
     * Garantiza que el directorio exista.
     */
    public function ensureDirectory(
        string $directory,
    ): void;

    /**
     * Elimina un archivo si existe.
     */
    public function delete(
        string $path,
    ): void;
}
