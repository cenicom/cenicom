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
     * @param array<string, string> $variables
     */
    public function render(string $stub, array $variables = []): string
    {
        $path = $this->resolvePath($stub);

        $content = $this->load($path);

        return $this->replace($content, $variables);
    }

    /**
     * Obtiene la ruta completa del stub.
     */
    private function resolvePath(string $stub): string
    {
        $stub = ltrim($stub, '/\\');

        if (!str_ends_with($stub, '.stub')) {
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
        if (!is_file($path)) {
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
     * @param array<string, string> $variables
     */
    private function replace(
        string $content,
        array $variables
    ): string {
        foreach ($variables as $key => $value) {
            $content = preg_replace(
                '/{{\s*' . preg_quote($key, '/') . '\s*}}/',
                $value,
                $content
            );
        }

        return $content;
    }
}
