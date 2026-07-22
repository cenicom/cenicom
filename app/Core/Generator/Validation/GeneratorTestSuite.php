<?php

declare(strict_types=1);

namespace App\Core\Generator\Validation;

use App\Core\Generator\Results\GeneratorResult;
use RuntimeException;

final readonly class GeneratorTestSuite
{
    /**
     * Ejecuta todas las validaciones posteriores
     * a la generación de archivos.
     */
    public function validate(
        GeneratorResult $result
    ): void {

        foreach ($result->created() as $file) {
            $this->validateFile($file);
        }
    }

    /**
     * Valida un archivo generado.
     */
    private function validateFile(
        string $file
    ): void {

        if (! is_file($file)) {
            throw new RuntimeException(
                "Archivo inexistente: {$file}"
            );
        }

        if (! is_readable($file)) {
            throw new RuntimeException(
                "Archivo no legible: {$file}"
            );
        }

        $content = file_get_contents($file);

        if ($content === false) {
            throw new RuntimeException(
                "No fue posible leer {$file}"
            );
        }

        if (trim($content) === '') {
            throw new RuntimeException(
                "Archivo vacío: {$file}"
            );
        }

        if (
            str_contains($content, '[[')
            || str_contains($content, ']]')
        ) {
            throw new RuntimeException(
                "Placeholder encontrado en {$file}"
            );
        }
    }
}
