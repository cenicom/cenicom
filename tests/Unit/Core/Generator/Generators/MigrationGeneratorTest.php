<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Generator\Generators;

use App\Core\Generator\Factories\ModuleDataFactory;
use App\Core\Generator\Generators\MigrationGenerator;
use App\Core\Generator\Presentation\Factory\PresentationFactory;
use App\Core\Generator\Processors\MigrationFieldProcessor;
use App\Core\Generator\Support\FileWriter;
use App\Core\Generator\Support\StubManager;
use App\Core\Generator\Validation\GeneratorValidator;
use Tests\TestCase;

final class MigrationGeneratorTest extends TestCase
{
    public function test_generates_migration_file(): void
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
                'routeName' => 'currencies',
                'viewPrefix' => 'currencies',
            ],

            'fields' => [
                [
                    'name' => 'name',
                    'type' => 'string',
                    'required' => true,
                ],

                [
                    'name' => 'symbol',
                    'type' => 'string',
                    'required' => true,
                ],
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


    public function test_generator_supports_only_modules_with_table(): void
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
                'routeName' => 'currencies',
                'viewPrefix' => 'currencies',
            ],
        ]);

        $this->assertTrue(
            $generator->supports($module)
        );
    }


    public function test_generates_valid_migration(): void
    {
        $generator = $this->createGenerator();

        $module = $this->module();

        $generator->generate($module);

        $file = $module->migrationFile();

        $this->assertFileExists($file);

        $content = file_get_contents($file);

        $this->assertNotFalse($content);

        $this->assertStringContainsString(
            "Schema::create('currencies'",
            $content
        );

        $this->assertStringContainsString(
            "\$table->string('name')",
            $content
        );

        $this->assertStringContainsString(
            "\$table->string('symbol')",
            $content
        );

        $this->assertStringContainsString(
            "\$table->timestamps()",
            $content
        );
    }


    private function module()
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

            'fields' => [
                [
                    'name' => 'name',
                    'type' => 'string',
                    'required' => true,
                ],

                [
                    'name' => 'symbol',
                    'type' => 'string',
                    'required' => true,
                ],
            ],
        ]);
    }


    private function createGenerator(): MigrationGenerator
    {
        return new MigrationGenerator(
            new StubManager(),
            new FileWriter(),
            new PresentationFactory(),
            new GeneratorValidator([]),
            new MigrationFieldProcessor(),
        );
    }
}
