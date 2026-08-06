<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Generator\Generators;

use App\Core\Generator\Generators\ActionGenerator;
use App\Core\Generator\Presentation\Factory\PresentationFactory;
use App\Core\Generator\Support\FileWriter;
use App\Core\Generator\Support\StubManager;
use App\Core\Generator\Validation\GeneratorValidator;
use Tests\Support\GeneratorTestCase;


final class ActionGeneratorTest extends GeneratorTestCase
{
    public function test_generates_action_file(): void
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

    public function test_generates_valid_action(): void
    {
        $generator = $this->createGenerator();

        $module = $this->createModuleData();

        $generator->generate($module);

        $file = $module->actionPath();

        $this->assertFileExists($file);

        $content = file_get_contents($file);

        $this->assertNotFalse($content);

        $this->assertStringContainsString(
            'final readonly class CurrencyAction',
            $content
        );

        $this->assertStringContainsString(
            'CurrencyServiceInterface',
            $content
        );

        $this->assertStringContainsString(
            'public function create',
            $content
        );

        $this->assertStringContainsString(
            'public function update',
            $content
        );

        $this->assertStringContainsString(
            'public function destroy',
            $content
        );
    }

    private function createGenerator(): ActionGenerator
    {
        return new ActionGenerator(
            new StubManager(),
            new FileWriter(),
            new PresentationFactory(),
            new GeneratorValidator([])
        );
    }
}
