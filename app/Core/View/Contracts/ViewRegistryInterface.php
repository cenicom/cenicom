<?php

declare(strict_types=1);

namespace App\Core\View\Contracts;

interface ViewRegistryInterface
{
    /**
     * Registra un namespace de vistas.
     */
    public function register(
        string $namespace,
        string $path,
    ): void;

    /**
     * Obtiene la ruta registrada para un namespace.
     */
    public function path(
        string $namespace
    ): ?string;

    /**
     * @return array<string, string>
     */
    public function all(): array;

    /**
     * Limpia el registro completo.
     */
    public function clear(): void;
}
