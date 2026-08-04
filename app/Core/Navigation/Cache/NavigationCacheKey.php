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

    public static function role(
        string $role
    ): string {
        return self::PREFIX
            . '.tree.role.'
            . $role;
    }

    public static function institution(
        int|string $institutionId
    ): string {
        return self::PREFIX
            . '.tree.institution.'
            . $institutionId;
    }

    public static function all(): string
    {
        return self::PREFIX . '.*';
    }
}
