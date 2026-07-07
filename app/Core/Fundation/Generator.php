<?php

namespace App\Core\Foundation;

use Illuminate\Support\Facades\File;

abstract class Generator
{
    protected function stub(string $name): string
    {
        return File::get(
            base_path("stubs/cn/{$name}.stub")
        );
    }

    protected function write(
        string $path,
        string $content
    ): void {

        File::ensureDirectoryExists(dirname($path));

        File::put($path, $content);
    }
}
