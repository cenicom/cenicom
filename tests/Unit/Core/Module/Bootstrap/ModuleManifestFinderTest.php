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

        $path = base_path(
            'tests/Fixtures/Modules'
        );

        $finder = new ModuleManifestFinder(
            $path
        );


        // Act

        $manifests = $finder->find();


        // Assert

        $this->assertNotEmpty(
            $manifests
        );

        $this->assertContains(
            str_replace(
                '\\',
                '/',
                realpath(
                    $path . '/TestModule/module.php'
                )
            ),
            array_map(
                static fn(string $manifest): string =>
                str_replace('\\', '/', $manifest),
                $manifests
            )
        );
    }
}
