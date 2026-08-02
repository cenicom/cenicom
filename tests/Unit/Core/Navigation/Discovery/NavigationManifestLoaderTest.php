<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Navigation\Discovery;

use App\Core\Navigation\Discovery\NavigationManifestLoader;
use App\Core\Navigation\DTO\NavigationManifestData;
use Tests\TestCase;
use RuntimeException;

final class NavigationManifestLoaderTest extends TestCase
{
    public function test_loads_valid_manifest(): void
    {
        $loader = new NavigationManifestLoader();

        $manifest = $loader->load(
            __DIR__ . '/Fixtures/valid_navigation.php'
        );

        $this->assertInstanceOf(
            NavigationManifestData::class,
            $manifest
        );

        $this->assertSame(
            [],
            $manifest->groups
        );

        $this->assertSame(
            [],
            $manifest->items
        );
    }

    public function test_throws_exception_when_manifest_does_not_exist(): void
    {
        $this->expectException(
            RuntimeException::class
        );

        (new NavigationManifestLoader())->load(
            __DIR__ . '/Fixtures/not_found.php'
        );
    }
}
