<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Navigation\Services;

use App\Core\Navigation\Cache\Contracts\NavigationCacheInterface;
use App\Core\Navigation\Cache\NavigationCacheKey;
use App\Core\Navigation\Contracts\NavigationBuilderInterface;
use App\Core\Navigation\DTO\NavigationTreeData;
use App\Core\Navigation\Services\NavigationService;
use App\Core\Security\Contracts\IdentityInterface;
use PHPUnit\Framework\TestCase;

final class NavigationServiceCacheTest extends TestCase
{
    public function test_returns_navigation_tree_from_cache_when_available(): void
    {
        $identity = $this->createMock(
            IdentityInterface::class
        );

        $identity
            ->method('id')
            ->willReturn(10);

        $tree = new NavigationTreeData(
            nodes: []
        );

        $cache = $this->createMock(
            NavigationCacheInterface::class
        );

        $cache
            ->expects($this->once())
            ->method('get')
            ->with(
                NavigationCacheKey::user(10)
            )
            ->willReturn($tree);

        $builder = $this->createMock(
            NavigationBuilderInterface::class
        );

        $builder
            ->expects($this->never())
            ->method('build');

        $service = new NavigationService(
            $builder,
            $cache
        );

        self::assertSame(
            $tree,
            $service->tree($identity)
        );
    }


    public function test_builds_and_caches_tree_when_cache_is_empty(): void
    {
        $identity = $this->createMock(
            IdentityInterface::class
        );

        $identity
            ->method('id')
            ->willReturn(10);

        $tree = new NavigationTreeData(
            nodes: []
        );

        $cache = $this->createMock(
            NavigationCacheInterface::class
        );

        $cache
            ->expects($this->once())
            ->method('get')
            ->with(
                NavigationCacheKey::user(10)
            )
            ->willReturn(null);

        $cache
            ->expects($this->once())
            ->method('put')
            ->with(
                NavigationCacheKey::user(10),
                $tree
            );

        $builder = $this->createMock(
            NavigationBuilderInterface::class
        );

        $builder
            ->expects($this->once())
            ->method('build')
            ->with($identity)
            ->willReturn($tree);

        $service = new NavigationService(
            $builder,
            $cache
        );

        self::assertSame(
            $tree,
            $service->tree($identity)
        );
    }
}
