<?php

declare(strict_types=1);

namespace App\Core\Navigation\Cache;

use App\Core\Navigation\Cache\Contracts\NavigationCacheInterface;
use App\Core\Navigation\Cache\Contracts\NavigationCacheInvalidatorInterface;

final readonly class NavigationCacheInvalidator
    implements NavigationCacheInvalidatorInterface
{
    public function __construct(
        private NavigationCacheInterface $cache,
    ) {
    }

    /**
     * Invalida la navegación de una identidad concreta.
     */
    public function user(
        int|string $identityId
    ): void {
        $this->cache->forget(
            NavigationCacheKey::user($identityId)
        );
    }

    /**
     * Invalida la navegación asociada a un rol.
     *
     * Un cambio de rol puede afectar a múltiples usuarios.
     * Mientras no exista un índice role -> identities en caché,
     * la estrategia segura es invalidar todo.
     */
    public function role(
        string $role
    ): void {
        $this->all();
    }

    /**
     * Invalida todo el caché de navegación.
     */
    public function all(): void
    {
        $this->cache->clear();
    }
}
