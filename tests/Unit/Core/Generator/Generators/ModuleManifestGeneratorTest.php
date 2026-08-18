<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Generator\Generators;

use App\Core\Generator\Builders\ModuleManifestBuilder;
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
            "'description' => 'Currency module'",
            $content
        );

        $this->assertStringContainsString(
            "'model' => 'Currency'",
            $content
        );

        $this->assertStringContainsString(
            "'routePrefix' => 'currencies'",
            $content
        );

        $this->assertStringContainsString(
            "'routeName' => 'currencies'",
            $content
        );

        $this->assertStringContainsString(
            "'permissions' => true",
            $content
        );

        $this->assertStringContainsString(
            "'menu' => true",
            $content
        );

        $this->assertStringContainsString(
            "'api' => true",
            $content
        );

        $this->assertStringContainsString(
            "'tests' => true",
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
            new ModuleManifestBuilder(),
        );
    }
}
