<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Module\Bootstrap;

use App\Core\Module\Discovery\ModuleManifestFinder;
use Tests\TestCase;

final class ModuleManifestFinderTest extends TestCase
{
    public function test_finds_module_manifest_from_fixture_directory(): void
    {
        // Arrange
        $finder = new ModuleManifestFinder(
            base_path('tests/Fixtures/Modules')
        );

        // Act
        $manifests = iterator_to_array($finder->find());

        // Assert
        $this->assertNotEmpty($manifests);

        $normalized = array_map(
            static fn (string $path): string => realpath($path),
            $manifests
        );

        $expected = realpath(
            base_path('tests/Fixtures/Modules/Blog/module.php')
        );

        $this->assertContains(
            $expected,
            $normalized
        );
    }
}
