<?php

declare(strict_types=1);

namespace App\Core\Navigation\Services;

use App\Core\Navigation\Cache\Contracts\NavigationCacheInterface;
use App\Core\Navigation\Cache\NavigationCacheKey;
use App\Core\Navigation\Contracts\NavigationBuilderInterface;
use App\Core\Navigation\Contracts\NavigationServiceInterface;
use App\Core\Navigation\DTO\NavigationTreeData;
use App\Core\Security\Contracts\IdentityInterface;

final readonly class NavigationService implements NavigationServiceInterface
{
    public function __construct(
        private NavigationBuilderInterface $builder,
        private NavigationCacheInterface $cache,
    ) {}

    /**
     * Obtiene el árbol de navegación filtrado por identidad.
     */
    public function tree(
        IdentityInterface $identity
    ): NavigationTreeData {
        $key = NavigationCacheKey::user(
            $identity->id()
        );

        $tree = $this->cache->get($key);

        if ($tree !== null) {
            return $tree;
        }

        $tree = $this->builder->build(
            $identity
        );

        $this->cache->put(
            $key,
            $tree
        );

        return $tree;
    }
}
