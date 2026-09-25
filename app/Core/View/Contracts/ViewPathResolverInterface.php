<?php

declare(strict_types=1);

namespace App\Core\View\Contracts;

interface ViewPathResolverInterface
{
    /**
     * Resuelve una ruta de vistas declarada por un módulo
     * a una ruta física absoluta.
     */
    public function resolve(string $path): string;
}
