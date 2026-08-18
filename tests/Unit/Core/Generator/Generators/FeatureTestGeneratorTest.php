<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Generator\Generators;

use App\Core\Generator\Builders\FeatureTestBuilder;
use App\Core\Generator\Generators\FeatureTestGenerator;
use App\Core\Generator\Presentation\Factory\PresentationFactory;
use App\Core\Generator\Support\FileWriter;
use App\Core\Generator\Support\StubManager;
use App\Core\Generator\Validation\GeneratorValidator;
use Tests\Support\GeneratorTestCase;

final class FeatureTestGeneratorTest extends GeneratorTestCase
{
    public function test_generates_feature_test_file(): void
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

    public function test_generates_valid_feature_test(): void
    {
        $generator = $this->createGenerator();

        $module = $this->createModuleData();

        $generator->generate($module);

        $file = $module->featureTestPath();

        $this->assertFileExists($file);

        $content = file_get_contents($file);

        $this->assertNotFalse($content);

        $this->assertStringContainsString(
            'final class CurrencyFeatureTest',
            $content
        );

        $this->assertStringContainsString(
            'extends TestCase',
            $content
        );

        $this->assertStringContainsString(
            'use RefreshDatabase;',
            $content
        );

        $this->assertStringContainsString(
            "route('currencies.index')",
            $content
        );

        $this->assertStringContainsString(
            "route('currencies.create')",
            $content
        );
    }

    private function createGenerator(): FeatureTestGenerator
    {
        return new FeatureTestGenerator(
            new StubManager(),
            new FileWriter(),
            new PresentationFactory(),
            new GeneratorValidator([]),
            new FeatureTestBuilder(),
        );
    }
}
