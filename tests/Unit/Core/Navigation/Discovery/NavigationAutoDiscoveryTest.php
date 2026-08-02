<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Navigation\Discovery;

use App\Core\Navigation\Contracts\NavigationManifestDiscoveryInterface;
use App\Core\Navigation\Contracts\NavigationRegistryInterface;
use App\Core\Navigation\Discovery\NavigationAutoDiscovery;
use Tests\TestCase;

final class NavigationAutoDiscoveryTest extends TestCase
{
    public function test_module_navigation_is_discovered_automatically(): void
    {
        $discovery = $this->app->make(
            NavigationManifestDiscoveryInterface::class
        );

        $discovery->discover();

        $registry = $this->app->make(
            NavigationRegistryInterface::class
        );

        $this->assertNotEmpty(
            $registry->groups()
        );

        $this->assertNotEmpty(
            $registry->items()
        );
    }

    public function test_returns_empty_array_when_no_navigation_files_exist(): void
    {
        $discovery = new NavigationAutoDiscovery(
            __DIR__ . '/Fixtures/EmptyModules'
        );

        self::assertSame(
            [],
            $discovery->discover()
        );
    }
}
