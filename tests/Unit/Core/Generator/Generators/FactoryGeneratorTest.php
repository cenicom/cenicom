<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Generator\Generators;

use App\Core\Generator\Builders\FactoryBuilder;
use App\Core\Generator\Generators\FactoryGenerator;
use App\Core\Generator\Presentation\Factory\PresentationFactory;
use App\Core\Generator\Support\FileWriter;
use App\Core\Generator\Support\StubManager;
use App\Core\Generator\Validation\GeneratorValidator;
use Tests\Support\GeneratorTestCase;

final class FactoryGeneratorTest extends GeneratorTestCase
{
    public function test_generates_factory_file(): void
    {
        $generator = $this->createGenerator();

        $result = $generator->generate(
            $this->createModuleData()
        );

        $this->assertTrue(
            $result->isSuccessful()
        );

        $this->assertTrue(
            $result->hasCreatedFiles()
        );

        $this->assertSame(
            1,
            $result->createdCount()
        );
    }

    public function test_generator_supports_any_module(): void
    {
        $generator = $this->createGenerator();

        $this->assertTrue(
            $generator->supports(
                $this->createModuleData()
            )
        );
    }

    public function test_generates_valid_factory(): void
    {
        $generator = $this->createGenerator();

        $module = $this->createModuleData();

        $generator->generate($module);

        $file = $module->factoryPath();

        $this->assertFileExists($file);

        $content = file_get_contents($file);

        $this->assertNotFalse($content);

        $this->assertStringContainsString(
            'final class CurrencyFactory',
            $content
        );

        $this->assertStringContainsString(
            'extends Factory',
            $content
        );

        $this->assertStringContainsString(
            'use App\\Modules\\Currency\\Models\\Currency;',
            $content
        );

        $this->assertStringContainsString(
            'protected $model = Currency::class;',
            $content
        );
    }

    private function createGenerator(): FactoryGenerator
    {
        return new FactoryGenerator(
            new StubManager(),
            new FileWriter(),
            new PresentationFactory(),
            new GeneratorValidator([]),
            new FactoryBuilder(),
        );
    }
}
