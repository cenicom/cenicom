<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Generator\Generators;

use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\Factories\ModuleDataFactory;
use App\Core\Generator\Generators\ActionGenerator;
use App\Core\Generator\Presentation\Factory\PresentationFactory;
use App\Core\Generator\Support\FileWriter;
use App\Core\Generator\Support\StubManager;
use App\Core\Generator\Validation\GeneratorValidator;
use Tests\TestCase;

final class ActionGeneratorTest extends TestCase
{
    public function test_generates_action_file(): void
    {
        $generator = $this->createGenerator();

        $result = $generator->generate(
            $this->module()
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
                $this->module()
            )
        );
    }

    public function test_generates_valid_action(): void
    {
        $generator = $this->createGenerator();

        $module = $this->module();

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

    private function module(): ModuleData
    {
        return (new ModuleDataFactory())->create([
            'identity' => [
                'name' => 'Currency',
                'singular' => 'currency',
                'plural' => 'currencies',
                'table' => 'currencies',
                'description' => 'Currency module',
            ],

            'generation' => [
                'routePrefix' => 'currencies',
                'routeName' => 'currencies',
                'viewPrefix' => 'currencies',
            ],

            'fields' => [],
            'columns' => [],
        ]);
    }
}
