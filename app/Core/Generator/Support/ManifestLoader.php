<?php

declare(strict_types=1);

namespace App\Core\Generator\Support;

use JsonException;
use RuntimeException;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Carga los manifiestos de módulos del CN Generator.
 *
 * Responsable únicamente de localizar, leer, validar y convertir
 * el archivo JSON de definición del módulo.
 *
 * @package App\Core\Generator\Support
 * @since 1.0.0
 */
final readonly class ManifestLoader
{
    /**
     * Directorio donde residen los manifiestos.
     */
    public function __construct(
        private string $manifestPath = 'modules',
    ) {
    }

    /**
     * Carga un manifiesto.
     *
     * @return array<string,mixed>
     *
     * @throws RuntimeException
     */
    public function load(string $module): array
    {
        $file = $this->manifestFile($module);

        if (! is_file($file)) {
            throw new RuntimeException(
                "Manifest not found: {$file}"
            );
        }

        $contents = file_get_contents($file);

        if ($contents === false) {
            throw new RuntimeException(
                "Unable to read manifest: {$file}"
            );
        }

        try {
            /** @var array<string,mixed> $manifest */
            $manifest = json_decode(
                $contents,
                true,
                512,
                JSON_THROW_ON_ERROR
            );
        } catch (JsonException $exception) {
            throw new RuntimeException(
                "Invalid JSON manifest: {$file}",
                previous: $exception
            );
        }

        return $this->validate($manifest);
    }

    /**
     * Verifica si existe un manifiesto.
     */
    public function exists(string $module): bool
    {
        return is_file(
            $this->manifestFile($module)
        );
    }

    /**
     * Obtiene la ruta absoluta del manifiesto.
     */
    public function manifestFile(string $module): string
    {
        return base_path(
            "{$this->manifestPath}/{$module}.json"
        );
    }

    /**
     * Valida y normaliza el manifiesto.
     *
     * @param array<string,mixed> $manifest
     *
     * @return array<string,mixed>
     */
    private function validate(array $manifest): array
    {
        if (! isset($manifest['identity'])) {
            throw new RuntimeException(
                'Manifest requires the "identity" section.'
            );
        }

        if (! isset($manifest['generation'])) {
            throw new RuntimeException(
                'Manifest requires the "generation" section.'
            );
        }

        $manifest['fields'] ??= [];
        $manifest['relations'] ??= [];
        $manifest['validation'] ??= [];
        $manifest['permissions'] ??= [];
        $manifest['navigation'] ??= [];
        $manifest['metadata'] ??= [];

        return $manifest;
    }
}
