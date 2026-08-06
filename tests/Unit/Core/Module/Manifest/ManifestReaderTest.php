<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Module\Manifest;

use App\Core\Module\Manifest\ManifestException;
use App\Core\Module\Manifest\ManifestFactory;
use App\Core\Module\Manifest\ManifestReader;
use App\Core\Module\Manifest\ManifestValidator;
use App\Core\Module\Manifest\ManifestWriter;
use App\Core\Module\Manifest\ModuleManifest;
use Tests\TestCase;

final class ManifestReaderTest extends TestCase
{
    private ManifestReader $reader;

    private ManifestWriter $writer;

    private ManifestFactory $factory;

    private string $directory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->factory = new ManifestFactory();

        $this->writer = new ManifestWriter();

        $this->reader = new ManifestReader(
            $this->factory,
            new ManifestValidator()
        );

        $this->directory = sys_get_temp_dir()
            . DIRECTORY_SEPARATOR
            . uniqid('manifest_', true);

        mkdir(
            $this->directory,
            0777,
            true
        );
    }

    protected function tearDown(): void
    {
        $this->deleteDirectory(
            $this->directory
        );

        parent::tearDown();
    }

    public function test_reads_manifest(): void
    {
        $manifest = $this->createManifest();

        $path = $this->manifestPath();

        $this->writer->write(
            $manifest,
            $path
        );

        $loaded = $this->reader->read(
            $path
        );

        $this->assertInstanceOf(
            ModuleManifest::class,
            $loaded
        );

        $this->assertSame(
            'Currency',
            $loaded->name()
        );

        $this->assertSame(
            'currency',
            $loaded->slug()
        );
    }

    public function test_reads_complete_manifest(): void
    {
        $manifest = $this->factory->create([
            'name' => 'Currency',
            'slug' => 'currency',
            'description' => 'Currency module',
            'version' => '1.0.0',
            'providers' => [
                'App\\Providers\\CurrencyServiceProvider',
            ],
            'dependencies' => [
                'Core',
            ],
            'permissions' => [
                'currencies.view',
                'currencies.create',
            ],
            'navigation' => [
                [
                    'group' => 'Administration',
                    'label' => 'Currencies',
                    'route' => 'currencies.index',
                    'icon' => 'bi-currency-dollar',
                ],
            ],
        ]);

        $path = $this->manifestPath();

        $this->writer->write(
            $manifest,
            $path
        );

        $loaded = $this->reader->read(
            $path
        );

        $this->assertSame(
            $manifest->toArray(),
            $loaded->toArray()
        );
    }

    public function test_throws_exception_when_manifest_does_not_exist(): void
    {
        $this->expectException(
            ManifestException::class
        );

        $this->reader->read(
            $this->manifestPath()
        );
    }

    public function test_throws_exception_when_json_is_invalid(): void
    {
        $path = $this->manifestPath();

        file_put_contents(
            $path,
            '{invalid json}'
        );

        $this->expectException(
            ManifestException::class
        );

        $this->reader->read(
            $path
        );
    }

    public function test_throws_exception_when_json_is_not_array(): void
    {
        $path = $this->manifestPath();

        file_put_contents(
            $path,
            '"currency"'
        );

        $this->expectException(
            ManifestException::class
        );

        $this->reader->read(
            $path
        );
    }

    public function test_returns_valid_module_manifest(): void
    {
        $manifest = $this->createManifest();

        $path = $this->manifestPath();

        $this->writer->write(
            $manifest,
            $path
        );

        $loaded = $this->reader->read(
            $path
        );

        $this->assertInstanceOf(
            ModuleManifest::class,
            $loaded
        );

        $this->assertSame(
            '1.0.0',
            $loaded->version()
        );
    }

    private function createManifest(): ModuleManifest
    {
        return $this->factory->create([
            'name' => 'Currency',
            'slug' => 'currency',
            'description' => 'Currency module',
            'version' => '1.0.0',
        ]);
    }

    private function manifestPath(): string
    {
        return $this->directory
            . DIRECTORY_SEPARATOR
            . 'module.json';
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
