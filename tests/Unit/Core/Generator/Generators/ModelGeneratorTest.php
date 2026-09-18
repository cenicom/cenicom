<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Generator\Generators;

use App\Core\Generator\Generators\ModelGenerator;
use App\Core\Generator\Presentation\Factory\PresentationFactory;
use App\Core\Generator\Support\FileWriter;
use App\Core\Generator\Support\StubManager;
use App\Core\Generator\Validation\GeneratorValidator;
use App\Core\Generator\Builders\ModelBuilder;
use Tests\Support\GeneratorTestCase;


final class ModelGeneratorTest extends GeneratorTestCase
{
    public function test_generates_model_file(): void
    {

        $generator = $this->createGenerator();

        $module = $this->createModuleData([
            'fields' => [],
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

        $module = $this->createModuleData();

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

    public function test_generates_has_many_relationship_with_imported_model(): void
    {
        $generator = $this->createGenerator();

        $module = $this->createModuleData([
            'identity' => [
                'name' => 'Country',
                'singular' => 'country',
                'plural' => 'countries',
                'table' => 'countries',
                'description' => 'Country module',
            ],
            'relations' => [
                [
                    'type' => 'hasMany',
                    'method' => 'states',
                    'model' => 'App\\Modules\\State\\Models\\State',
                ],
            ],
        ]);

        $result = $generator->generate($module);

        $this->assertTrue($result->isSuccessful());

        $file = $module->modelPath();

        $this->assertFileExists($file);

        $content = file_get_contents($file);

        $this->assertNotFalse($content);

        $this->assertStringContainsString(
            'use App\\Modules\\State\\Models\\State;',
            $content
        );

        $this->assertStringContainsString(
            'public function states(): HasMany',
            $content
        );

        $this->assertStringContainsString(
            'return $this->hasMany(State::class);',
            $content
        );

        $this->assertStringNotContainsString(
            'return $this->hasMany(App\\Modules\\State\\Models\\State::class);',
            $content
        );
    }


    private function createGenerator(): ModelGenerator
    {
        return new ModelGenerator(
            new StubManager(),
            new FileWriter(),
            new PresentationFactory(),
            new GeneratorValidator([]),
            new ModelBuilder(),
        );
    }
}
