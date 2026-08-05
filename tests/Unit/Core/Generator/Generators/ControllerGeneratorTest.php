<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Generator\Generators;

use App\Core\Generator\Factories\ModuleDataFactory;
use App\Core\Generator\Generators\ControllerGenerator;
use App\Core\Generator\Presentation\Factory\PresentationFactory;
use App\Core\Generator\Support\Controller\ControllerBuilder;
use App\Core\Generator\Support\FileWriter;
use App\Core\Generator\Support\StubManager;
use App\Core\Generator\Validation\GeneratorValidator;
use Tests\Support\GeneratorTestCase;


final class ControllerGeneratorTest extends GeneratorTestCase
{
    public function test_generates_controller_file(): void
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

    private function createGenerator(): ControllerGenerator
    {
        return new ControllerGenerator(
            new StubManager(),
            new FileWriter(),
            new PresentationFactory(),
            new GeneratorValidator([]),
            new ControllerBuilder(),
        );
    }
}
