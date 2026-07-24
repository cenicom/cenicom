<?php

declare(strict_types=1);

namespace App\Core\Generator\Support;

use RuntimeException;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Gestiona la escritura segura de archivos generados por el
 * CN Generator.
 *
 * Centraliza la creación de directorios, validación de
 * existencia, sobrescritura controlada y persistencia de
 * archivos del sistema.
 *
 * @package App\Core\Generator\Support
 * @since 1.0.0
 */
final class FileWriter
{
    /**
     * Escribe un archivo en disco.
     *
     * @throws RuntimeException
     */
    public function write(
        string $path,
        string $contents,
        bool $overwrite = false
    ): void {
        $directory = dirname($path);

        $this->ensureDirectory($directory);

        if ($this->exists($path) && ! $overwrite) {
            throw new RuntimeException(
                sprintf('El archivo [%s] ya existe.', $path)
            );
        }

        if ($contents === '') {
            throw new RuntimeException(
                sprintf(
                    'No se puede escribir contenido vacío en [%s].',
                    $path
                )
            );
        }

        $result = file_put_contents($path, $contents);
        if ($result === false) {
            throw new RuntimeException(
                sprintf(
                    'No fue posible escribir el archivo [%s].',
                    $path
                )
            );
        }
    }

    /**
     * Determina si un archivo existe.
     */
    public function exists(string $path): bool
    {
        return is_file($path);
    }

    /**
     * Garantiza que el directorio exista.
     *
     * @throws RuntimeException
     */
    public function ensureDirectory(string $directory): void
    {
        if (is_dir($directory)) {
            return;
        }

        if (! mkdir($directory, 0755, true) && ! is_dir($directory)) {
            throw new RuntimeException(
                sprintf(
                    'No fue posible crear el directorio [%s].',
                    $directory
                )
            );
        }
    }

    /**
     * Elimina un archivo.
     *
     * @throws RuntimeException
     */
    public function delete(string $path): void
    {
        if (! $this->exists($path)) {
            return;
        }

        if (! unlink($path)) {
            throw new RuntimeException(
                sprintf(
                    'No fue posible eliminar el archivo [%s].',
                    $path
                )
            );
        }
    }
}
