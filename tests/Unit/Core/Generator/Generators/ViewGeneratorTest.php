<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Generator\Generators;

use App\Core\Generator\Builders\ViewBuilder;
use App\Core\Generator\Factories\ModuleDataFactory;
use App\Core\Generator\Generators\ViewGenerator;
use App\Core\Generator\Presentation\Factory\PresentationFactory;
use App\Core\Generator\Presentation\Renderers\ComponentRenderer;
use App\Core\Generator\Presentation\Renderers\ShowRenderer;
use App\Core\Generator\Presentation\Renderers\TableRenderer;
use App\Core\Generator\Support\FileWriter;
use App\Core\Generator\Support\StubManager;
use App\Core\Generator\Support\Contracts\FileWriterInterface;
use RuntimeException;
use Tests\Support\GeneratorTestCase;


final class ViewGeneratorTest extends GeneratorTestCase
{
    public function test_generates_exactly_the_expected_views(): void
    {
        $generator = $this->createGenerator();

        $module = $this->createModuleData();

        $result = $generator->generate($module);

        $this->assertTrue(
            $result->isSuccessful()
        );

        $expected = [
            'index.blade.php',
            'create.blade.php',
            'edit.blade.php',
            'show.blade.php',
            '_form.blade.php',
            'export.blade.php',
        ];

        $viewPath = $module->viewPath();

        foreach ($expected as $target) {
            $this->assertFileExists(
                $viewPath . DIRECTORY_SEPARATOR . $target
            );
        }

        $files = array_values(
            array_filter(
                scandir($viewPath),
                static fn(string $file): bool =>
                $file !== '.'
                    && $file !== '..'
            )
        );

        sort($files);
        $expectedSorted = $expected;
        sort($expectedSorted);

        $this->assertSame(
            $expectedSorted,
            $files
        );

        $this->assertSame(
            6,
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

    public function test_generates_views_inside_module_view_path(): void
    {
        $generator = $this->createGenerator();

        $module = $this->createModuleData();

        $generator->generate($module);

        $viewPath = $module->viewPath();

        $this->assertDirectoryExists(
            $viewPath
        );

        foreach (
            [
                'index.blade.php',
                'create.blade.php',
                'edit.blade.php',
                'show.blade.php',
                '_form.blade.php',
                'export.blade.php',
            ] as $target
        ) {
            $path = $viewPath
                . DIRECTORY_SEPARATOR
                . $target;

            $this->assertFileExists($path);

            $this->assertStringStartsWith(
                $viewPath . DIRECTORY_SEPARATOR,
                $path
            );
        }
    }

    public function test_each_target_is_generated_from_the_expected_stub(): void
    {
        $generator = $this->createGenerator();

        $module = $this->createModuleData();

        $generator->generate($module);

        $viewPath = $module->viewPath();

        $index = file_get_contents(
            $viewPath . DIRECTORY_SEPARATOR . 'index.blade.php'
        );

        $create = file_get_contents(
            $viewPath . DIRECTORY_SEPARATOR . 'create.blade.php'
        );

        $edit = file_get_contents(
            $viewPath . DIRECTORY_SEPARATOR . 'edit.blade.php'
        );

        $show = file_get_contents(
            $viewPath . DIRECTORY_SEPARATOR . 'show.blade.php'
        );

        $form = file_get_contents(
            $viewPath . DIRECTORY_SEPARATOR . '_form.blade.php'
        );

        $export = file_get_contents(
            $viewPath . DIRECTORY_SEPARATOR . 'export.blade.php'
        );

        $this->assertNotFalse($index);
        $this->assertNotFalse($create);
        $this->assertNotFalse($edit);
        $this->assertNotFalse($show);
        $this->assertNotFalse($form);
        $this->assertNotFalse($export);

        $this->assertStringContainsString(
            '<x-cn.crud',
            $index
        );

        $this->assertStringContainsString(
            'Crear currency',
            $create
        );

        $this->assertStringContainsString(
            'Editar currency',
            $edit
        );

        $this->assertStringContainsString(
            'Detalle de currency',
            $show
        );

        $this->assertStringContainsString(
            '<x-cn.group columns="2">',
            $form
        );

        $this->assertStringContainsString(
            'Export currencies',
            $export
        );
    }

    public function test_write_errors_are_added_to_generator_result(): void
    {
        $fileWriter = $this->createMock(
            FileWriterInterface::class
        );

        $fileWriter
            ->method('write')
            ->willThrowException(
                new RuntimeException('Simulated write failure.')
            );

        $generator = new ViewGenerator(
            new StubManager(),
            $fileWriter,
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
        );

        $result = $generator->generate(
            $this->createModuleData()
        );

        $this->assertFalse(
            $result->isSuccessful()
        );

        $this->assertSame(
            0,
            $result->createdCount()
        );
    }

    public function test_edit_view_targets_update_route(): void
    {
        $generator = $this->createGenerator();

        $module = $this->createModuleData();

        $generator->generate($module);

        $path = $module->viewPath()
            . DIRECTORY_SEPARATOR
            . 'edit.blade.php';

        $content = file_get_contents($path);

        $this->assertNotFalse($content);

        $this->assertStringContainsString(
            "route('currencies.update', \$currency)",
            $content
        );

        $this->assertStringContainsString(
            "method=\"POST\"",
            $content
        );

        $this->assertStringContainsString(
            '@csrf',
            $content
        );

        $this->assertStringContainsString(
            "@method('PUT')",
            $content
        );

        $this->assertStringNotContainsString(
            "route('currencies.store')",
            $content
        );
    }

    private function createGenerator(): ViewGenerator
    {
        return new ViewGenerator(
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
        );
    }
}
