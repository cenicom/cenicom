<?php

namespace App\Support\Generators;

use Illuminate\Support\Facades\File;

abstract class BaseGenerator
{
    /**
     * Lee un stub.
     */
    protected function stub(string $name): string
    {
        $path = base_path("stubs/cn/{$name}.stub");

        if (! File::exists($path)) {
            throw new \RuntimeException("Stub {$name} no encontrado.");
        }

        return File::get($path);
    }

    /**
     * Reemplaza variables.
     */
    protected function render(string $stub, array $variables): string
    {
        foreach ($variables as $key => $value) {

            $stub = str_replace(
                "{{ {$key} }}",
                $value,
                $stub
            );

        }

        return $stub;
    }

    /**
     * Escribe el archivo.
     */
    protected function write(string $path, string $content): void
    {
        File::ensureDirectoryExists(dirname($path));

        File::put($path, $content);
    }
}
