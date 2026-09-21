<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Generator\Generators;

use App\Core\Generator\Generators\ViewDefinitionGenerator;
use App\Core\Generator\Presentation\Factory\PresentationFactory;
use App\Core\Generator\Support\FileWriter;
use App\Core\Generator\Support\StubManager;
use App\Core\Generator\Validation\GeneratorValidator;
use Tests\Support\GeneratorTestCase;

final class ViewDefinitionGeneratorTest extends GeneratorTestCase
{
    public function test_generates_view_definition_file(): void
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

    public function test_generates_valid_view_definition(): void
    {
        $generator = $this->createGenerator();

        $module = $this->createModuleData();

        $generator->generate($module);

        $file = $module->viewDefinitionPath();

        $this->assertFileExists($file);

        $content = file_get_contents($file);

        $this->assertNotFalse($content);

        $this->assertStringContainsString(
            'namespace App\\Modules\\Currency\\View;',
            $content
        );

        $this->assertStringContainsString(
            'use App\\Core\\View\\Contracts\\ViewDefinitionInterface;',
            $content
        );

        $this->assertStringContainsString(
            'use App\\Core\\View\\Contracts\\ViewRegistrarInterface;',
            $content
        );

        $this->assertStringContainsString(
            'final class CurrencyView implements ViewDefinitionInterface',
            $content
        );

        $this->assertStringContainsString(
            'public function register(',
            $content
        );

        $this->assertStringContainsString(
            "'currencies',",
            $content
        );

        $this->assertStringContainsString(
            "'app/Modules/Currency/Resources/Views',",
            $content
        );
    }

    private function createGenerator(): ViewDefinitionGenerator
    {
        return new ViewDefinitionGenerator(
            new StubManager(),
            new FileWriter(),
            new PresentationFactory(),
            new GeneratorValidator([]),
        );
    }
}
