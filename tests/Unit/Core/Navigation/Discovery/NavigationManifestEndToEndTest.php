<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Navigation\Discovery;

use App\Core\Navigation\Contracts\NavigationManifestFinderInterface;
use App\Core\Navigation\Contracts\NavigationManifestLoaderInterface;
use App\Core\Navigation\Contracts\NavigationManifestRegistrarInterface;
use App\Core\Navigation\Contracts\NavigationRegistryInterface;
use App\Core\Navigation\DTO\NavigationGroupData;
use App\Core\Navigation\DTO\NavigationItemData;
use Tests\TestCase;

final class NavigationManifestEndToEndTest extends TestCase
{
    public function test_demo_module_navigation_manifest_is_discovered_loaded_and_registered(): void
    {
        $finder = $this->app->make(
            NavigationManifestFinderInterface::class
        );

        $loader = $this->app->make(
            NavigationManifestLoaderInterface::class
        );

        $registrar = $this->app->make(
            NavigationManifestRegistrarInterface::class
        );

        $registry = $this->app->make(
            NavigationRegistryInterface::class
        );

        $registry->clear();

        $manifests = $finder->discover();

        $demoManifest = base_path(
            'modules/DemoModule/navigation.php'
        );

        $normalizedManifests = array_map(
            static fn(string $path): string =>
            str_replace('\\', '/', $path),
            $manifests
        );

        $normalizedDemoManifest = str_replace(
            '\\',
            '/',
            $demoManifest
        );

        $manifest = $loader->load(
            $demoManifest
        );

        $this->assertSame(
            'DemoModule',
            $manifest->module
        );

        $registrar->register(
            $manifest
        );

        $groups = $registry->groups();
        $items = $registry->items();

        $this->assertArrayHasKey(
            'demo',
            $groups
        );

        $this->assertInstanceOf(
            NavigationGroupData::class,
            $groups['demo']
        );

        $this->assertSame(
            'Demo',
            $groups['demo']->label()
        );

        $this->assertArrayHasKey(
            'demo.dashboard',
            $items
        );

        $this->assertInstanceOf(
            NavigationItemData::class,
            $items['demo.dashboard']
        );

        $this->assertSame(
            'Dashboard Demo',
            $items['demo.dashboard']->label()
        );

        $this->assertSame(
            'demo.dashboard',
            $items['demo.dashboard']->route()
        );

        $this->assertSame(
            'demo',
            $items['demo.dashboard']->group()
        );
    }
}
