<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Generator\Generators;

use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\Factories\ModuleDataFactory;
use App\Core\Generator\Generators\ModelGenerator;
use App\Core\Generator\Presentation\Factory\PresentationFactory;
use App\Core\Generator\Support\FileWriter;
use App\Core\Generator\Support\StubManager;
use App\Core\Generator\Validation\GeneratorValidator;
use Tests\TestCase;

final class ModelGeneratorTest extends TestCase
{
    public function test_generates_model_file(): void
    {
        $generator = $this->createGenerator();

        $module = (new ModuleDataFactory())->create([
            'identity' => [
                'name' => 'Currency',
                'singular' => 'currency',
                'plural' => 'currencies',
                'table' => 'currencies',
                'description' => 'Currency module',
            ],
            'generation' => [
                'routePrefix' => 'currencies',
                'routeName'   => 'currencies',
                'viewPrefix'  => 'currencies',
            ],
        ]);

        $result = $generator->generate($module);

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

        $module = (new ModuleDataFactory())->create([
            'identity' => [
                'name' => 'Test',
                'singular' => 'test',
                'plural' => 'tests',
                'table' => 'tests',
                'description' => 'Test module',
            ],
            'generation' => [
                'routePrefix' => 'tests',
                'routeName'   => 'tests',
                'viewPrefix'  => 'tests',
            ],
        ]);

        $this->assertTrue(
            $generator->supports($module)
        );
    }

    public function test_generates_valid_model(): void
    {
        $generator = $this->createGenerator();

        $module = $this->module();

        $generator->generate($module);

        $file = $module->modelPath();

        $this->assertFileExists($file);

        $content = file_get_contents($file);

        $this->assertNotFalse($content);

        $this->assertStringContainsString(
            'final class Currency extends Model',
            $content
        );

        $this->assertStringContainsString(
            "protected \$table = 'currencies';",
            $content
        );

        $this->assertStringContainsString(
            'protected function casts(): array',
            $content
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

    private function createGenerator(): ModelGenerator
    {
        return new ModelGenerator(
            new StubManager(),
            new FileWriter(),
            new PresentationFactory(),
            new GeneratorValidator([]),
        );
    }
}
