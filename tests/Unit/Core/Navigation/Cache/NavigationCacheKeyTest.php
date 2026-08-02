<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Navigation\Cache;

use App\Core\Navigation\Cache\NavigationCacheKey;
use PHPUnit\Framework\TestCase;

final class NavigationCacheKeyTest extends TestCase
{
    public function test_generates_default_tree_key(): void
    {
        self::assertSame(
            'cn.navigation.tree',
            NavigationCacheKey::tree()
        );
    }

    public function test_generates_user_tree_key(): void
    {
        self::assertSame(
            'cn.navigation.tree.user.10',
            NavigationCacheKey::user(10)
        );
    }

    public function test_generates_role_tree_key(): void
    {
        self::assertSame(
            'cn.navigation.tree.role.admin',
            NavigationCacheKey::role('admin')
        );
    }

    public function test_generates_institution_tree_key(): void
    {
        self::assertSame(
            'cn.navigation.tree.institution.100',
            NavigationCacheKey::institution(100)
        );
    }
}
