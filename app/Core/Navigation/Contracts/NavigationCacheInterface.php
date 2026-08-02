<?php

declare(strict_types=1);

namespace App\Core\Navigation\Contracts;

use App\Core\Navigation\DTO\NavigationTreeData;

interface NavigationCacheInterface
{
    public function get(string $key): ?NavigationTreeData;

    public function put(
        string $key,
        NavigationTreeData $tree,
        int $ttl
    ): void;

    public function forget(string $key): void;
}
