<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Generator\Generators;


use App\Core\Generator\Builders\MigrationBuilder;
use App\Core\Generator\Generators\MigrationGenerator;
use App\Core\Generator\Presentation\Factory\PresentationFactory;
use App\Core\Generator\Processors\MigrationFieldProcessor;
use App\Core\Generator\Support\FileWriter;
use App\Core\Generator\Support\GeneratorExecutionContext;
use App\Core\Generator\Support\MigrationFileResolver;
use App\Core\Generator\Support\StubManager;
use App\Core\Generator\Validation\GeneratorValidator;
use Tests\Support\GeneratorTestCase;


final class MigrationGeneratorTest extends GeneratorTestCase
{
    public function test_generates_migration_file(): void
    {
        $generator = $this->createGenerator();

        $module = $this->createModuleData([
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

        $module = $this->createModuleData([
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

        $module = $this->createModuleData();

        $result = $generator->generate($module);

        $this->assertCount(
            1,
            $result->created()
        );

        $file = $result->created()[0];

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

    public function test_force_updates_existing_migration_instead_of_creating_duplicate(): void
    {
        $generator = $this->createGenerator();

        $module = $this->createModuleData();

        $firstResult = $generator->generate($module);

        $this->assertSame(
            1,
            $firstResult->createdCount()
        );

        $this->assertSame(
            0,
            $firstResult->updatedCount()
        );

        $this->assertCount(
            1,
            $firstResult->created()
        );

        $generatedMigration = $firstResult->created()[0];

        $this->assertFileExists(
            $generatedMigration
        );

        $existingMigration = $this->migrationsPath()
            . DIRECTORY_SEPARATOR
            . '2020_01_01_000000_create_currencies_table.php';

        $this->assertTrue(
            rename(
                $generatedMigration,
                $existingMigration
            )
        );

        $this->assertFileExists(
            $existingMigration
        );

        $this->assertFileDoesNotExist(
            $generatedMigration
        );

        $context = app(GeneratorExecutionContext::class);

        $context->setForce(true);

        $secondResult = $generator->generate($module);

        $this->assertSame(
            0,
            $secondResult->createdCount()
        );

        $this->assertSame(
            1,
            $secondResult->updatedCount()
        );

        $this->assertSame(
            $existingMigration,
            $secondResult->updated()[0]
        );

        $this->assertFileExists(
            $existingMigration
        );

        $migrationFiles = glob(
            $this->migrationsPath()
                . DIRECTORY_SEPARATOR
                . '*_create_currencies_table.php'
        );

        $this->assertNotFalse(
            $migrationFiles
        );

        $this->assertCount(
            1,
            $migrationFiles
        );
    }

    private function createGenerator(): MigrationGenerator
    {
        return new MigrationGenerator(
            new StubManager(),
            new FileWriter(),
            new PresentationFactory(),
            new GeneratorValidator([]),
            new MigrationBuilder(
                new MigrationFieldProcessor(),
            ),
            new MigrationFileResolver(),
        );
    }
}
