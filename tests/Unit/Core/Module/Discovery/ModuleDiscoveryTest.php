<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Module\Discovery;

use App\Core\Module\Discovery\DiscoveryException;
use App\Core\Module\Discovery\ModuleDiscovery;
use App\Core\Module\Discovery\ModuleFinder;
use App\Core\Module\Manifest\ManifestFactory;
use App\Core\Module\Manifest\ManifestReader;
use App\Core\Module\Manifest\ManifestValidator;
use PHPUnit\Framework\TestCase;

final class ModuleDiscoveryTest extends TestCase
{
    private ModuleDiscovery $discovery;

    private string $directory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->directory = sys_get_temp_dir()
            . DIRECTORY_SEPARATOR
            . uniqid('modules_', true);

        mkdir($this->directory, 0777, true);

        $this->discovery = new ModuleDiscovery(
            new ModuleFinder(),
            new ManifestReader(
                new ManifestFactory(),
                new ManifestValidator()
            ),
            new ManifestValidator()
        );
    }

    protected function tearDown(): void
    {
        $this->deleteDirectory($this->directory);

        parent::tearDown();
    }

    public function test_discovers_single_module(): void
    {
        $this->createModule('Currency');

        $modules = $this->discovery->discover(
            $this->directory
        );

        $this->assertCount(
            1,
            $modules
        );

        $this->assertSame(
            'Currency',
            $modules[0]->name()
        );
    }

    public function test_discovers_multiple_modules(): void
    {
        $this->createModule('Currency');
        $this->createModule('Country');
        $this->createModule('Language');

        $modules = $this->discovery->discover(
            $this->directory
        );

        $this->assertCount(
            3,
            $modules
        );
    }

    public function test_returns_module_manifest_instances(): void
    {
        $this->createModule('Currency');

        $modules = $this->discovery->discover(
            $this->directory
        );

        $this->assertContainsOnlyInstancesOf(
            \App\Core\Module\Manifest\ModuleManifest::class,
            $modules
        );
    }

    public function test_throws_when_manifest_is_missing(): void
    {
        mkdir(
            $this->directory
            . DIRECTORY_SEPARATOR
            . 'Currency'
        );

        $this->expectException(
            DiscoveryException::class
        );

        $this->discovery->discover(
            $this->directory
        );
    }

    public function test_throws_when_manifest_is_invalid(): void
    {
        $module = $this->directory
            . DIRECTORY_SEPARATOR
            . 'Currency';

        mkdir($module);

        file_put_contents(
            $module . DIRECTORY_SEPARATOR . 'module.php',
            '{}'
        );

        $this->expectException(
            \Throwable::class
        );

        $this->discovery->discover(
            $this->directory
        );
    }

    /**
     * Crea un módulo temporal con un module.php válido.
     */
    private function createModule(string $name): void
    {
        $module = $this->directory
            . DIRECTORY_SEPARATOR
            . $name;

        mkdir($module);

        file_put_contents(
            $module
            . DIRECTORY_SEPARATOR
            . 'module.php',
            json_encode(
                [
                    'name' => $name,
                    'slug' => strtolower($name),
                    'description' => $name . ' module',
                    'version' => '1.0.0',
                    'providers' => [],
                    'dependencies' => [],
                    'permissions' => [],
                    'navigation' => [],
                ],
                JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR
            )
        );
    }

    private function deleteDirectory(string $directory): void
    {
        if (! is_dir($directory)) {
            return;
        }

        $items = array_diff(
            scandir($directory),
            ['.', '..']
        );

        foreach ($items as $item) {

            $path = $directory
                . DIRECTORY_SEPARATOR
                . $item;

            if (is_dir($path)) {
                $this->deleteDirectory($path);
            } else {
                unlink($path);
            }
        }

        rmdir($directory);
    }
}
