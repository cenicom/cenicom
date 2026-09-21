<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Generator\Generators;

use App\Core\Generator\Generators\ModuleManifestGenerator;
use App\Core\Generator\Presentation\Factory\PresentationFactory;
use App\Core\Generator\Support\FileWriter;
use App\Core\Generator\Support\StubManager;
use App\Core\Generator\Validation\GeneratorValidator;
use Tests\Support\GeneratorTestCase;

final class ModuleManifestGeneratorTest extends GeneratorTestCase
{
    public function test_generates_module_manifest_file(): void
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

    public function test_generates_valid_module_manifest(): void
    {
        $generator = $this->createGenerator();

        $module = $this->createModuleData();

        $generator->generate($module);

        $file = $module->moduleManifestPath();

        $this->assertFileExists($file);

        $content = file_get_contents($file);

        $this->assertNotFalse($content);

        $this->assertStringContainsString(
            "'name' => 'Currency'",
            $content
        );

        $this->assertStringContainsString(
            "'namespace' => 'App\\Modules\\Currency'",
            $content
        );

        $this->assertStringContainsString(
            "'providers' => [],",
            $content
        );

        $this->assertStringContainsString(
            "'view_definitions' => [",
            $content
        );

        $this->assertStringContainsString(
            'App\\Modules\\Currency\\View\\CurrencyView::class',
            $content
        );

        $this->assertStringContainsString(
            "'enabled' => true,",
            $content
        );
    }

    private function createGenerator(): ModuleManifestGenerator
    {
        return new ModuleManifestGenerator(
            new StubManager(),
            new FileWriter(),
            new PresentationFactory(),
            new GeneratorValidator([]),
        );
    }
}
