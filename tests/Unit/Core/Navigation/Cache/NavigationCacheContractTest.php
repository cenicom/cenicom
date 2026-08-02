<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Navigation\Cache;

use App\Core\Navigation\Cache\Contracts\NavigationCacheInterface;
use PHPUnit\Framework\TestCase;

final class NavigationCacheContractTest extends TestCase
{
    public function test_navigation_cache_contract_exists(): void
    {
        self::assertTrue(
            interface_exists(NavigationCacheInterface::class)
        );
    }

    public function test_navigation_cache_contract_defines_required_methods(): void
    {
        $reflection = new \ReflectionClass(
            NavigationCacheInterface::class
        );

        self::assertTrue(
            $reflection->hasMethod('get')
        );

        self::assertTrue(
            $reflection->hasMethod('put')
        );

        self::assertTrue(
            $reflection->hasMethod('forget')
        );

        self::assertTrue(
            $reflection->hasMethod('has')
        );
    }
}
