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
            ->method('authenticated')
            ->willReturn(true);

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
            ->method('authenticated')
            ->willReturn(true);

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

    public function test_returns_guest_navigation_tree_from_cache_when_available(): void
    {
        $identity = $this->createMock(
            IdentityInterface::class
        );

        $identity
            ->method('authenticated')
            ->willReturn(false);

        $identity
            ->method('id')
            ->willReturn(null);

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
                NavigationCacheKey::guest()
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

    public function test_builds_and_caches_guest_navigation_tree_when_cache_is_empty(): void
    {
        $identity = $this->createMock(
            IdentityInterface::class
        );

        $identity
            ->method('authenticated')
            ->willReturn(false);

        $identity
            ->method('id')
            ->willReturn(null);

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
                NavigationCacheKey::guest()
            )
            ->willReturn(null);

        $cache
            ->expects($this->once())
            ->method('put')
            ->with(
                NavigationCacheKey::guest(),
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

    public function test_rejects_authenticated_identity_without_id(): void
    {
        $identity = $this->createMock(
            IdentityInterface::class
        );

        $identity
            ->method('authenticated')
            ->willReturn(true);

        $identity
            ->method('id')
            ->willReturn(null);

        $cache = $this->createMock(
            NavigationCacheInterface::class
        );

        $builder = $this->createMock(
            NavigationBuilderInterface::class
        );

        $service = new NavigationService(
            $builder,
            $cache
        );

        $this->expectException(\LogicException::class);

        $this->expectExceptionMessage(
            'An authenticated identity must have an identifier.'
        );

        $service->tree($identity);
    }
}

