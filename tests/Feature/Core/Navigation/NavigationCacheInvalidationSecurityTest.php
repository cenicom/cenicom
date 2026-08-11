<?php

declare(strict_types=1);

namespace Tests\Feature\Core\Navigation;

use App\Core\Navigation\Cache\Contracts\NavigationCacheInterface;
use App\Core\Navigation\Cache\Contracts\NavigationCacheInvalidatorInterface;
use App\Core\Navigation\Cache\NavigationCacheKey;
use App\Core\Navigation\DTO\NavigationTreeData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class NavigationCacheInvalidationSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app
            ->make(NavigationCacheInterface::class)
            ->clear();
    }

    public function test_user_navigation_cache_can_be_invalidated(): void
    {
        $cache = $this->app->make(
            NavigationCacheInterface::class
        );

        $key = NavigationCacheKey::user(100);

        $tree = new NavigationTreeData();

        $cache->put(
            $key,
            $tree
        );

        $this->assertTrue(
            $cache->has($key)
        );

        $this->app
            ->make(NavigationCacheInvalidatorInterface::class)
            ->user(100);

        $this->assertFalse(
            $cache->has($key)
        );
    }

    public function test_invalidating_one_user_does_not_remove_another_user_cache(): void
    {
        $cache = $this->app->make(
            NavigationCacheInterface::class
        );

        $cache->put(
            NavigationCacheKey::user(100),
            new NavigationTreeData()
        );

        $cache->put(
            NavigationCacheKey::user(200),
            new NavigationTreeData()
        );

        $this->app
            ->make(NavigationCacheInvalidatorInterface::class)
            ->user(100);

        $this->assertFalse(
            $cache->has(
                NavigationCacheKey::user(100)
            )
        );

        $this->assertTrue(
            $cache->has(
                NavigationCacheKey::user(200)
            )
        );
    }

    public function test_role_invalidation_clears_all_navigation_cache(): void
    {
        $cache = $this->app->make(
            NavigationCacheInterface::class
        );

        $cache->put(
            NavigationCacheKey::user(100),
            new NavigationTreeData()
        );

        $cache->put(
            NavigationCacheKey::user(200),
            new NavigationTreeData()
        );

        $cache->put(
            NavigationCacheKey::guest(),
            new NavigationTreeData()
        );

        $this->app
            ->make(NavigationCacheInvalidatorInterface::class)
            ->role('administrator');

        $this->assertFalse(
            $cache->has(
                NavigationCacheKey::user(100)
            )
        );

        $this->assertFalse(
            $cache->has(
                NavigationCacheKey::user(200)
            )
        );

        $this->assertFalse(
            $cache->has(
                NavigationCacheKey::guest()
            )
        );
    }

    public function test_global_navigation_invalidation_clears_all_cache(): void
    {
        $cache = $this->app->make(
            NavigationCacheInterface::class
        );

        $cache->put(
            NavigationCacheKey::user(100),
            new NavigationTreeData()
        );

        $cache->put(
            NavigationCacheKey::user(200),
            new NavigationTreeData()
        );

        $cache->put(
            NavigationCacheKey::guest(),
            new NavigationTreeData()
        );

        $this->app
            ->make(NavigationCacheInvalidatorInterface::class)
            ->all();

        $this->assertFalse(
            $cache->has(
                NavigationCacheKey::user(100)
            )
        );

        $this->assertFalse(
            $cache->has(
                NavigationCacheKey::user(200)
            )
        );

        $this->assertFalse(
            $cache->has(
                NavigationCacheKey::guest()
            )
        );
    }

    public function test_invalidation_service_is_resolvable_from_container(): void
    {
        $service = $this->app->make(
            NavigationCacheInvalidatorInterface::class
        );

        $this->assertInstanceOf(
            NavigationCacheInvalidatorInterface::class,
            $service
        );
    }
}
