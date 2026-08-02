<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Navigation\Cache;

use App\Core\Navigation\Cache\NavigationCache;
use App\Core\Navigation\DTO\NavigationTreeData;
use Illuminate\Contracts\Cache\Repository;
use Mockery;
use PHPUnit\Framework\TestCase;

final class NavigationCacheTest extends TestCase
{
    public function test_stores_navigation_tree(): void
    {
        $repository = Mockery::mock(Repository::class);

        $tree = new NavigationTreeData();

        $repository
            ->shouldReceive('put')
            ->once()
            ->with(
                'cn.navigation.tree',
                $tree,
                3600
            );

        $cache = new NavigationCache($repository);

        $cache->put(
            'cn.navigation.tree',
            $tree
        );

        self::assertTrue(true);
    }

    public function test_retrieves_cached_value(): void
    {
        $repository = Mockery::mock(Repository::class);

        $tree = new NavigationTreeData();

        $repository
            ->shouldReceive('get')
            ->once()
            ->with('cn.navigation.tree')
            ->andReturn($tree);

        $cache = new NavigationCache($repository);

        self::assertSame(
            $tree,
            $cache->get('cn.navigation.tree')
        );
    }

    public function test_checks_existing_key(): void
    {
        $repository = Mockery::mock(Repository::class);

        $repository
            ->shouldReceive('has')
            ->once()
            ->with('cn.navigation.tree')
            ->andReturnTrue();

        $cache = new NavigationCache($repository);

        self::assertTrue(
            $cache->has('cn.navigation.tree')
        );
    }

    public function test_removes_cached_value(): void
    {
        $repository = Mockery::mock(Repository::class);

        $repository
            ->shouldReceive('forget')
            ->once()
            ->with('cn.navigation.tree');

        $cache = new NavigationCache($repository);

        $cache->forget(
            'cn.navigation.tree'
        );

        self::assertTrue(true);
    }

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }
}
