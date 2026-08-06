<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Module\Manifest;

use App\Core\Module\Manifest\ManifestException;
use App\Core\Module\Manifest\ManifestFactory;
use App\Core\Module\Manifest\ManifestWriter;
use PHPUnit\Framework\TestCase;

final class ManifestWriterTest extends TestCase
{
    private ManifestWriter $writer;

    private ManifestFactory $factory;

    private string $directory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->writer = new ManifestWriter();
        $this->factory = new ManifestFactory();

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
        if (is_dir($this->directory)) {
            $this->deleteDirectory($this->directory);
        }

        parent::tearDown();
    }

    public function test_writes_manifest_file(): void
    {
        $manifest = $this->factory->create([
            'name' => 'Currency',
            'slug' => 'currency',
            'description' => 'Currency module',
            'version' => '1.0.0',
        ]);

        $path = $this->directory
            . DIRECTORY_SEPARATOR
            . 'module.json';

        $this->writer->write(
            $manifest,
            $path
        );

        $this->assertFileExists($path);
    }

    public function test_creates_directory_automatically(): void
    {
        $manifest = $this->factory->create([
            'name' => 'Currency',
            'slug' => 'currency',
            'description' => 'Currency module',
            'version' => '1.0.0',
        ]);

        $path = $this->directory
            . DIRECTORY_SEPARATOR
            . 'Modules'
            . DIRECTORY_SEPARATOR
            . 'Currency'
            . DIRECTORY_SEPARATOR
            . 'module.json';

        $this->writer->write(
            $manifest,
            $path
        );

        $this->assertDirectoryExists(dirname($path));
        $this->assertFileExists($path);
    }

    public function test_writes_valid_json(): void
    {
        $manifest = $this->factory->create([
            'name' => 'Currency',
            'slug' => 'currency',
            'description' => 'Currency module',
            'version' => '1.0.0',
        ]);

        $path = $this->directory
            . DIRECTORY_SEPARATOR
            . 'module.json';

        $this->writer->write(
            $manifest,
            $path
        );

        $content = file_get_contents($path);

        $this->assertNotFalse($content);

        $decoded = json_decode(
            $content,
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        $this->assertSame(
            'Currency',
            $decoded['name']
        );

        $this->assertSame(
            'currency',
            $decoded['slug']
        );

        $this->assertSame(
            '1.0.0',
            $decoded['version']
        );
    }

    public function test_overwrites_existing_manifest(): void
    {
        $manifest = $this->factory->create([
            'name' => 'Currency',
            'slug' => 'currency',
            'description' => 'Currency module',
            'version' => '1.0.0',
        ]);

        $path = $this->directory
            . DIRECTORY_SEPARATOR
            . 'module.json';

        $this->assertNotFalse(
            file_put_contents(
                $path,
                '{}'
            )
        );

        $this->writer->write(
            $manifest,
            $path
        );

        $content = file_get_contents($path);

        $this->assertNotFalse($content);

        $this->assertStringContainsString(
            '"Currency"',
            $content
        );

        //$this->assertDirectoryExists($this->directory);
    }

    public function test_throws_exception_when_path_is_invalid(): void
    {
        $manifest = $this->factory->create([
            'name' => 'Currency',
            'slug' => 'currency',
            'description' => 'Currency module',
            'version' => '1.0.0',
        ]);

        $this->expectException(
            ManifestException::class
        );

        $this->writer->write(
            $manifest,
            ''
        );
    }

    private function deleteDirectory(string $directory): void
    {
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
