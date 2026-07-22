<?php

declare(strict_types=1);

namespace App\Core\Generator\Support;

use InvalidArgumentException;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Gestiona la localización, lectura y procesamiento de los
 * archivos Stub utilizados por el CN Generator.
 *
 * Centraliza el reemplazo de placeholders para la generación
 * automática de código fuente.
 *
 * @package App\Core\Generator\Support
 * @since 1.0.0
 */
final class StubManager
{
    /**
     * Directorio base de los stubs.
     */
    private const STUB_PATH = 'resources/stubs';

    /**
     * Renderiza un stub.
     *
     * @param array<string, mixed> $variables
     */
    public function render(
        string $stub,
        array $variables = []
    ): string {

        $path = $this->resolvePath($stub);

        $content = $this->load($path);

        $content = $this->replace(
            $content,
            $variables,
            $stub
        );

        return $content;
    }

    /**
     * Obtiene la ruta completa del stub.
     */
    private function resolvePath(string $stub): string
    {
        $stub = ltrim($stub, '/\\');

        if (! str_ends_with($stub, '.stub')) {
            $stub .= '.stub';
        }

        return base_path(
            self::STUB_PATH . DIRECTORY_SEPARATOR . $stub
        );
    }

    /**
     * Carga el contenido del stub.
     */
    private function load(string $path): string
    {

        if (! is_file($path)) {
            throw new InvalidArgumentException(
                sprintf('Stub [%s] no encontrado.', $path)
            );
        }

        $content = file_get_contents($path);

        if ($content === false) {
            throw new InvalidArgumentException(
                sprintf('No fue posible leer el stub [%s].', $path)
            );
        }

        return $content;
    }

    /**
     * Reemplaza los placeholders del stub.
     *
     * @param array<string, mixed> $variables
     */
    private function replace(
        string $content,
        array $variables,
        string $stub
    ): string {

        foreach ($variables as $key => $value) {

            if (is_array($value) || is_object($value)) {

                continue;
            }

            if (! is_scalar($value) && $value !== null) {

                throw new InvalidArgumentException(
                    sprintf(
                        'La variable [%s] del stub debe ser escalar. Se recibió [%s].',
                        $key,
                        get_debug_type($value)
                    )
                );
            }

            $content = preg_replace(
                '/\[\[\s*' . preg_quote($key, '/') . '\s*\]\]/',
                str_replace(
                    '$',
                    '\$',
                    (string) $value
                ),
                $content
            );
        }


        if (preg_match_all('/\[\[\s*(.*?)\s*\]\]/', $content, $matches)) {

            $placeholders = array_unique($matches[1]);

            throw new InvalidArgumentException(
                sprintf(
                    'Stub [%s] contiene placeholders sin resolver: %s',
                    $stub,
                    implode(', ', $placeholders)
                )
            );
        }


        return $content;
    }

    public function ensureExists(string $stub): bool
    {
        $path = $this->resolvePath($stub);

        if (! file_exists($path)) {
            throw new \RuntimeException(
                sprintf(
                    'Stub [%s] not found.',
                    $stub
                )
            );
        }

        return true;
    }
}
