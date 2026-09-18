<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Generator\Generators;


use App\Core\Generator\Builders\ViewBuilder;
use App\Core\Generator\Factories\ModuleDataFactory;
use App\Core\Generator\Generators\ServiceGenerator;
use App\Core\Generator\Generators\ViewGenerator;
use App\Core\Generator\Presentation\Factory\PresentationFactory;
use App\Core\Generator\Presentation\Renderers\ComponentRenderer;
use App\Core\Generator\Presentation\Renderers\ShowRenderer;
use App\Core\Generator\Presentation\Renderers\TableRenderer;
use App\Core\Generator\Support\FileWriter;
use App\Core\Generator\Support\GeneratorExecutionContext;
use App\Core\Generator\Support\StubManager;
use App\Core\Generator\Validation\GeneratorValidator;
use Tests\Support\GeneratorTestCase;


final class ServiceGeneratorTest extends GeneratorTestCase
{
    public function test_generates_service_file(): void
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

        $result = $generator->generate($module);

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
                'routeName' => 'tests',
                'viewPrefix' => 'tests',
            ],
        ]);

        $this->assertTrue(
            $generator->supports($module)
        );
    }

    public function test_generates_valid_service(): void
    {
        $generator = $this->createGenerator();

        $module = $this->createModuleData();

        $generator->generate($module);

        $file = $module->servicePath();

        $this->assertFileExists($file);

        $content = file_get_contents($file);

        $this->assertNotFalse($content);

        $this->assertStringContainsString(
            'class CurrencyService',
            $content
        );

        $this->assertStringContainsString(
            'extends BaseService',
            $content
        );

        $this->assertStringContainsString(
            'implements CurrencyServiceInterface',
            $content
        );

        $this->assertStringContainsString(
            'CurrencyRepositoryInterface $repository',
            $content
        );

        $this->assertStringContainsString(
            'parent::__construct($repository)',
            $content
        );
    }

    public function test_force_updates_existing_service(): void
    {
        $generator = $this->createGenerator();

        $module = $this->createModuleData();

        $first = $generator->generate($module);

        $this->assertTrue(
            $first->isSuccessful()
        );

        $file = $module->servicePath();

        $original = file_get_contents($file);

        $this->assertNotFalse($original);

        $modified = $original . PHP_EOL . '// modified';

        file_put_contents($file, $modified);

        $context = app(GeneratorExecutionContext::class);

        $context->setForce(true);

        $second = $generator->generate($module);

        $this->assertTrue(
            $second->isSuccessful()
        );

        $this->assertCount(
            0,
            $second->created()
        );

        $this->assertCount(
            0,
            $second->skipped()
        );

        $this->assertCount(
            1,
            $second->updated()
        );

        $this->assertSame(
            [$file],
            $second->updated()
        );

        $updated = file_get_contents($file);

        $this->assertNotFalse($updated);

        $this->assertSame(
            $original,
            $updated
        );

        $this->assertStringNotContainsString(
            '// modified',
            $updated
        );
    }

    public function test_force_updates_existing_views(): void
    {
        $executionContext = new GeneratorExecutionContext();

        $generator = new ViewGenerator(
            new StubManager(),
            new FileWriter(),
            new ViewBuilder(
                new PresentationFactory(),
                new ComponentRenderer(
                    new StubManager(),
                ),
                new TableRenderer(
                    new StubManager(),
                ),
                new ShowRenderer(
                    new StubManager(),
                ),
            ),
            $executionContext,
        );

        $module = $this->createModuleData();

        $first = $generator->generate($module);

        $this->assertSame(
            6,
            $first->createdCount()
        );

        $executionContext->setForce(true);

        $second = $generator->generate($module);

        $this->assertSame(
            0,
            $second->createdCount()
        );

        $this->assertSame(
            6,
            $second->updatedCount()
        );

        $this->assertSame(
            0,
            $second->skippedCount()
        );
    }

    private function createGenerator(): ServiceGenerator
    {
        return new ServiceGenerator(
            new StubManager(),
            new FileWriter(),
            new PresentationFactory(),
            new GeneratorValidator([])
        );
    }
}
