<?php

declare(strict_types=1);

namespace App\Core\View\Contracts;

interface ViewRegistrarInterface
{
    /**
     * Registra un namespace de vistas.
     */
    public function register(
        string $namespace,
        string $path,
    ): void;
}
