<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Generator\Support;

use App\Core\Generator\Support\BindingWriter;
use App\Core\Generator\Support\FileWriter;
use Tests\TestCase;

final class BindingWriterTest extends TestCase
{
    private string $configPath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->configPath = config_path('cn-bindings.php');

        if (file_exists($this->configPath)) {
            unlink($this->configPath);
        }
    }

    protected function tearDown(): void
    {
        if (file_exists($this->configPath)) {
            unlink($this->configPath);
        }

        parent::tearDown();
    }

    public function test_add_creates_binding(): void
    {
        $writer = new BindingWriter(
            new FileWriter()
        );

        $result = $writer->add(
            'App\\Core\\Contracts\\CurrencyRepositoryInterface',
            'App\\Core\\Repositories\\CurrencyRepository',
        );

        $this->assertTrue($result);
        $this->assertFileExists($this->configPath);

        $bindings = require $this->configPath;

        $this->assertSame(
            'App\\Core\\Repositories\\CurrencyRepository',
            $bindings[
                'App\\Core\\Contracts\\CurrencyRepositoryInterface'
            ]
        );
    }

    public function test_add_does_not_create_duplicate_binding(): void
    {
        $writer = new BindingWriter(
            new FileWriter()
        );

        $interface =
            'App\\Core\\Contracts\\CurrencyRepositoryInterface';

        $implementation =
            'App\\Core\\Repositories\\CurrencyRepository';

        $this->assertTrue(
            $writer->add($interface, $implementation)
        );

        $this->assertFalse(
            $writer->add($interface, $implementation)
        );

        $bindings = $writer->all();

        $this->assertCount(1, $bindings);
    }

    public function test_exists_returns_true_for_registered_binding(): void
    {
        $writer = new BindingWriter(
            new FileWriter()
        );

        $interface =
            'App\\Core\\Contracts\\CurrencyRepositoryInterface';

        $writer->add(
            $interface,
            'App\\Core\\Repositories\\CurrencyRepository',
        );

        $this->assertTrue(
            $writer->exists($interface)
        );
    }

    public function test_exists_returns_false_for_unknown_binding(): void
    {
        $writer = new BindingWriter(
            new FileWriter()
        );

        $this->assertFalse(
            $writer->exists(
                'App\\Core\\Contracts\\UnknownInterface'
            )
        );
    }

    public function test_remove_deletes_existing_binding(): void
    {
        $writer = new BindingWriter(
            new FileWriter()
        );

        $interface =
            'App\\Core\\Contracts\\CurrencyRepositoryInterface';

        $writer->add(
            $interface,
            'App\\Core\\Repositories\\CurrencyRepository',
        );

        $this->assertTrue(
            $writer->remove($interface)
        );

        $this->assertFalse(
            $writer->exists($interface)
        );
    }

    public function test_remove_returns_false_for_unknown_binding(): void
    {
        $writer = new BindingWriter(
            new FileWriter()
        );

        $this->assertFalse(
            $writer->remove(
                'App\\Core\\Contracts\\UnknownInterface'
            )
        );
    }

    public function test_bindings_are_sorted_alphabetically(): void
    {
        $writer = new BindingWriter(
            new FileWriter()
        );

        $writer->add(
            'App\\Core\\Contracts\\ZInterface',
            'App\\Core\\ZImplementation',
        );

        $writer->add(
            'App\\Core\\Contracts\\AInterface',
            'App\\Core\\AImplementation',
        );

        $bindings = $writer->all();

        $this->assertSame(
            [
                'App\\Core\\Contracts\\AInterface',
                'App\\Core\\Contracts\\ZInterface',
            ],
            array_keys($bindings)
        );
    }

    public function test_all_returns_all_bindings(): void
    {
        $writer = new BindingWriter(
            new FileWriter()
        );

        $writer->add(
            'App\\Core\\Contracts\\CurrencyRepositoryInterface',
            'App\\Core\\Repositories\\CurrencyRepository',
        );

        $writer->add(
            'App\\Core\\Contracts\\CurrencyServiceInterface',
            'App\\Core\\Services\\CurrencyService',
        );

        $bindings = $writer->all();

        $this->assertCount(2, $bindings);

        $this->assertArrayHasKey(
            'App\\Core\\Contracts\\CurrencyRepositoryInterface',
            $bindings
        );

        $this->assertArrayHasKey(
            'App\\Core\\Contracts\\CurrencyServiceInterface',
            $bindings
        );
    }
}
