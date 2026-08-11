<?php

declare(strict_types=1);

namespace App\Core\Navigation\Cache\Contracts;

interface NavigationCacheInvalidatorInterface
{
    /**
     * Invalida la navegación cacheada de una identidad.
     */
    public function user(
        int|string $identityId
    ): void;

    /**
     * Invalida la navegación relacionada con un rol.
     *
     * Actualmente utiliza invalidación global porque
     * un rol puede pertenecer a múltiples identidades.
     */
    public function role(
        string $role
    ): void;

    /**
     * Invalida todo el caché de navegación.
     */
    public function all(): void;
}
