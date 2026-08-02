<?php

declare(strict_types=1);

namespace App\Core\Navigation\Cache;

final class NavigationCacheKey
{
    private const PREFIX = 'cn.navigation';

    public static function user(
        int|string $identityId
    ): string {
        return self::PREFIX
            . '.tree.user.'
            . $identityId;
    }

    public static function tree(): string
    {
        return self::PREFIX . '.tree';
    }

    public static function all(): string
    {
        return self::PREFIX . '.*';
    }
}
