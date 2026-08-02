<?php

declare(strict_types=1);

namespace App\Core\Navigation\Cache;

use App\Core\Navigation\Cache\Contracts\NavigationCacheInterface;
use App\Core\Navigation\DTO\NavigationTreeData;
use Illuminate\Contracts\Cache\Repository;

final class NavigationCache implements NavigationCacheInterface
{
    private const TTL = 3600;

    public function __construct(
        private Repository $cache
    ) {
    }

    public function has(
        string $key
    ): bool {
        return $this->cache->has($key);
    }

    public function get(
        string $key
    ): ?NavigationTreeData {
        return $this->cache->get($key);
    }

    public function put(
        string $key,
        NavigationTreeData $tree
    ): void {
        $this->cache->put(
            $key,
            $tree,
            self::TTL
        );
    }

    public function forget(
        string $key
    ): void {
        $this->cache->forget($key);
    }

    public function clear(): void
    {
        $this->cache->clear();
    }
}
