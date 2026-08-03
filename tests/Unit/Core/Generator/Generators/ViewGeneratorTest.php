<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Generator\Generators;

use App\Core\Generator\Factories\ModuleDataFactory;
use App\Core\Generator\Generators\ViewGenerator;
use App\Core\Generator\Presentation\Factory\PresentationFactory;
use App\Core\Generator\Presentation\Renderers\ComponentRenderer;
use App\Core\Generator\Presentation\Renderers\ShowRenderer;
use App\Core\Generator\Presentation\Renderers\TableRenderer;
use App\Core\Generator\Results\GeneratorResult;
use App\Core\Generator\Support\FileWriter;
use App\Core\Generator\Support\StubManager;
use Tests\TestCase;

final class ViewGeneratorTest extends TestCase
{
    public function test_generates_all_views(): void
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

        $this->assertInstanceOf(
            GeneratorResult::class,
            $result
        );

        $this->assertTrue(
            $result->isSuccessful()
        );

        $this->assertGreaterThan(
            0,
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

    private function createGenerator(): ViewGenerator
    {
        return new ViewGenerator(
            new StubManager(),
            new FileWriter(),
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
        );
    }
}
