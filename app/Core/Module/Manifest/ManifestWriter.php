<?php

declare(strict_types=1);

namespace App\Core\Module\Manifest;

use JsonException;

/**
 * Responsable de persistir un ModuleManifest en disco.
 *
 * Convierte el manifiesto a JSON utilizando el contrato
 * JsonSerializable del DTO y lo almacena en la ruta indicada.
 */
final readonly class ManifestWriter
{
    /**
     * Responsable de persistir un ModuleManifest en disco.
     *
     * Convierte el manifiesto a JSON utilizando su representación
     * en arreglo (toArray()) y lo almacena en la ruta indicada.
     */
    public function write(ModuleManifest $manifest, string $path): void
    {

        $directory = dirname($path);

        if ($directory === '' || $directory === '.') {
            throw new ManifestException(
                'Invalid manifest path.'
            );
        }

        if (
            ! is_dir($directory)
            && ! mkdir($directory, 0777, true)
            && ! is_dir($directory)
        ) {
            throw new ManifestException(
                sprintf(
                    'Unable to create directory [%s].',
                    $directory
                )
            );
        }

        try {

            $json = json_encode(
                $manifest->toArray(),
                JSON_PRETTY_PRINT
                    | JSON_UNESCAPED_SLASHES
                    | JSON_UNESCAPED_UNICODE
                    | JSON_THROW_ON_ERROR
            );
        } catch (JsonException $exception) {

            throw new ManifestException(
                'Unable to encode manifest.',
                previous: $exception
            );
        }

        if (file_put_contents($path, $json) === false) {

            throw new ManifestException(
                sprintf(
                    'Unable to write manifest [%s].',
                    $path
                )
            );
        }
    }
}
