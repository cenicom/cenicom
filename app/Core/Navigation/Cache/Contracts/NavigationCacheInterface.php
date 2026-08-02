<?php

declare(strict_types=1);

namespace App\Core\Navigation\Cache\Contracts;

use App\Core\Navigation\DTO\NavigationTreeData;

interface NavigationCacheInterface
{
    /**
     * Determina si existe una clave.
     */
    public function has(string $key): bool;

    /**
     * Obtiene un valor almacenado.
     */
    public function get(string $key): ?NavigationTreeData;

    /**
     * Guarda un valor con tiempo de vida opcional.
     */
    public function put(
        string $key,
        NavigationTreeData $tree
    ): void;

    /**
     * Elimina un elemento del caché.
     */
    public function forget(string $key): void;

    public function clear(): void;

}
