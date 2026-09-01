<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Generator\Generators;


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

    public function test_generates_expected_controller_content(): void
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

        $this->assertTrue($result->isSuccessful());

        $path = $module->controllerPath();

        $this->assertFileExists($path);

        $content = file_get_contents($path);

        $this->assertIsString($content);

        $this->assertStringContainsString(
            'namespace ' . $module->controllerNamespace() . ';',
            $content
        );

        $this->assertStringContainsString(
            'use ' . $module->qualifiedServiceInterface() . ';',
            $content
        );

        $this->assertStringContainsString(
            'use ' . $module->qualifiedStoreRequest() . ';',
            $content
        );

        $this->assertStringContainsString(
            'use ' . $module->qualifiedUpdateRequest() . ';',
            $content
        );

        $this->assertStringContainsString(
            'use ' . $module->qualifiedModel() . ';',
            $content
        );

        $this->assertStringContainsString(
            'final class ' . $module->controllerClass() . ' extends Controller',
            $content
        );

        $this->assertStringContainsString(
            'private readonly ' . $module->serviceInterface() . ' $service',
            $content
        );

        $this->assertStringContainsString(
            'private readonly ' . $module->actionClass() . ' $action',
            $content
        );

        $this->assertStringContainsString(
            'use ' . $module->qualifiedAction() . ';',
            $content
        );

        $this->assertStringContainsString(
            "return view('{$module->viewPrefix()}.index'",
            $content
        );

        $this->assertStringContainsString(
            '$this->service->paginate(',
            $content
        );

        $this->assertStringContainsString(
            "return view('{$module->viewPrefix()}.create')",
            $content
        );

        $this->assertStringContainsString(
            '$this->action->create(',
            $content
        );

        $this->assertStringContainsString(
            '$request->validated()',
            $content
        );

        $this->assertStringContainsString(
            "->route('{$module->routeName()}.index')",
            $content
        );

        $this->assertStringContainsString(
            '$' . $module->variable() . '->getKey()',
            $content
        );

        $this->assertStringContainsString(
            '$this->action->create(',
            $content
        );

        $this->assertStringContainsString(
            '$this->action->update(',
            $content
        );

        $this->assertStringContainsString(
            '$this->action->update(',
            $content
        );

        $this->assertStringContainsString(
            '$' . $module->variable() . '->getKey(),',
            $content
        );

        $this->assertStringContainsString(
            '$this->action->delete(',
            $content
        );

        $this->assertStringContainsString(
            '$this->action->delete(',
            $content
        );

        $this->assertStringContainsString(
            '$' . $module->variable() . '->getKey()',
            $content
        );

        $this->assertStringNotContainsString(
            '$this->service->create(',
            $content
        );

        $this->assertStringNotContainsString(
            '$this->service->update(',
            $content
        );

        $this->assertStringNotContainsString(
            '$this->service->delete(',
            $content
        );

        $this->assertStringContainsString(
            '$this->service->paginate(',
            $content
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
