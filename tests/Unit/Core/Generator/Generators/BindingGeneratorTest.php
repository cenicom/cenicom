<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Generator\Generators;

use App\Core\Generator\Generators\BindingGenerator;
use App\Core\Generator\Support\BindingWriter;
use App\Core\Generator\Support\FileWriter;
use Tests\Support\GeneratorTestCase;

final class BindingGeneratorTest extends GeneratorTestCase
{
    private string $configPath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->configPath = config_path(
            'cn-bindings.php'
        );

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

    public function test_generates_module_bindings(): void
    {
        $generator = $this->createGenerator();

        $module = $this->createModuleData();

        $result = $generator->generate($module);

        $this->assertTrue(
            $result->isSuccessful()
        );

        $this->assertFileExists(
            $this->configPath
        );

        $bindings = require $this->configPath;

        $this->assertSame(
            'App\\Core\\Repositories\\CurrencyRepository',
            $bindings[
                'App\\Core\\Contracts\\CurrencyRepositoryInterface'
            ]
        );

        $this->assertSame(
            'App\\Core\\Services\\CurrencyService',
            $bindings[
                'App\\Core\\Contracts\\CurrencyServiceInterface'
            ]
        );
    }

    public function test_generator_supports_any_module(): void
    {
        $generator = $this->createGenerator();

        $module = $this->createModuleData([
            'identity' => [
                'name' => 'Test',
                'singular' => 'test',
                'plural' => 'tests',
                'table' => 'tests',
                'description' => 'Test module',
            ],

            'generation' => [
                'routePrefix' => 'tests',
                'routeName' => 'tests',
                'viewPrefix' => 'tests',
            ],
        ]);

        $this->assertTrue(
            $generator->supports($module)
        );
    }

    public function test_generates_two_bindings(): void
    {
        $generator = $this->createGenerator();

        $module = $this->createModuleData();

        $result = $generator->generate($module);

        $this->assertTrue(
            $result->isSuccessful()
        );

        $this->assertCount(
            2,
            $result->created()
        );

        $this->assertCount(
            0,
            $result->skipped()
        );

        $this->assertCount(
            2,
            require $this->configPath
        );
    }

    public function test_does_not_duplicate_existing_bindings(): void
    {
        $generator = $this->createGenerator();

        $module = $this->createModuleData();

        $first = $generator->generate($module);

        $this->assertTrue(
            $first->isSuccessful()
        );

        $second = $generator->generate($module);

        $this->assertTrue(
            $second->isSuccessful()
        );

        $this->assertCount(
            0,
            $second->created()
        );

        $this->assertCount(
            2,
            $second->skipped()
        );

        $bindings = require $this->configPath;

        $this->assertCount(
            2,
            $bindings
        );
    }

    private function createGenerator(): BindingGenerator
    {
        return new BindingGenerator(
            new BindingWriter(
                new FileWriter()
            )
        );
    }
}
