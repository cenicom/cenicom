<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Module\Discovery;

use App\Core\Module\Discovery\DiscoveryException;
use App\Core\Module\Discovery\ModuleFinder;
use PHPUnit\Framework\TestCase;

final class ModuleFinderTest extends TestCase
{
    private ModuleFinder $finder;

    private string $directory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->finder = new ModuleFinder();

        $this->directory = sys_get_temp_dir()
            . DIRECTORY_SEPARATOR
            . uniqid('modules_', true);

        mkdir($this->directory, 0777, true);
    }

    protected function tearDown(): void
    {
        $this->deleteDirectory($this->directory);

        parent::tearDown();
    }

    public function test_finds_modules(): void
    {
        mkdir($this->directory
        . DIRECTORY_SEPARATOR
        . 'Accounting');

        mkdir($this->directory
        . DIRECTORY_SEPARATOR
        . 'Inventory');

        mkdir($this->directory
        . DIRECTORY_SEPARATOR
        . 'Treasury');

        $modules = $this->finder->find(
            $this->directory
        );

        $this->assertCount(
            3,
            $modules
        );

        $this->assertContains(
            $this->directory
            . DIRECTORY_SEPARATOR
            . 'Accounting',
            $modules
        );

        $this->assertContains(
            $this->directory
            . DIRECTORY_SEPARATOR
            . 'Inventory',
            $modules
        );

        $this->assertContains(
            $this->directory
            . DIRECTORY_SEPARATOR
            . 'Treasury',
            $modules
        );
    }

    public function test_ignores_files(): void
    {
        mkdir($this->directory
        . DIRECTORY_SEPARATOR
        . 'Accounting');

        file_put_contents(
            $this->directory
            . DIRECTORY_SEPARATOR
            . 'README.md',
            ''
        );

        $modules = $this->finder->find(
            $this->directory
        );

        $this->assertCount(
            1,
            $modules
        );
    }

    public function test_ignores_hidden_directories(): void
    {
        mkdir($this->directory
        . DIRECTORY_SEPARATOR
        . 'Accounting');

        mkdir($this->directory
        . DIRECTORY_SEPARATOR
        . '.git');

        mkdir($this->directory
        . DIRECTORY_SEPARATOR
        . '.idea');

        $modules = $this->finder->find(
            $this->directory
        );

        $this->assertCount(
            1,
            $modules
        );

        $this->assertContains(
            $this->directory
            . DIRECTORY_SEPARATOR
            . 'Accounting',
            $modules
        );
    }

    public function test_throws_when_directory_not_found(): void
    {
        $this->expectException(
            DiscoveryException::class
        );

        $this->finder->find(
            $this->directory . '_missing'
        );
    }

    public function test_throws_when_no_modules_exist(): void
    {
        $this->expectException(
            DiscoveryException::class
        );

        $this->finder->find(
            $this->directory
        );
    }

    /**
     * El test de permisos no es portable entre
     * Windows y Linux, por lo que se omite.
     */
    public function test_directory_is_readable(): void
    {
        $this->assertTrue(
            is_readable($this->directory)
        );
    }

    /**
     * Elimina recursivamente un directorio temporal.
     */
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
