<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Generator\Generators;

use App\Core\Generator\Factories\ModuleDataFactory;
use App\Core\Generator\Generators\RepositoryInterfaceGenerator;
use App\Core\Generator\Presentation\Factory\PresentationFactory;
use App\Core\Generator\Support\FileWriter;
use App\Core\Generator\Support\StubManager;
use App\Core\Generator\Validation\GeneratorValidator;
use Tests\Support\GeneratorTestCase;


final class RepositoryInterfaceGeneratorTest extends GeneratorTestCase
{
    private string $generatedPath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->generatedPath = base_path(
            'tests/tmp/generated'
        );

        if (is_dir($this->generatedPath)) {
            $this->removeDirectory(
                $this->generatedPath
            );
        }
    }


    public function test_generates_repository_interface_file(): void
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


        $result = $generator->generate(
            $module
        );


        $this->assertTrue(
            $result->isSuccessful()
        );

        $this->assertTrue(
            $result->hasCreatedFiles()
        );

        $this->assertCount(
            1,
            $result->created()
        );

        $file = $result->created()[0];

        $this->assertFileExists($file);

        $content = file_get_contents($file);

        $this->assertStringContainsString(
            'interface CurrencyRepositoryInterface',
            $content
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


    private function createGenerator(): RepositoryInterfaceGenerator
    {
        return new RepositoryInterfaceGenerator(
            new StubManager(),
            new FileWriter(),
            new PresentationFactory(),
            new GeneratorValidator([]),
        );
    }


    private function removeDirectory(
        string $directory
    ): void {

        foreach (
            glob($directory . '/*') ?: []
            as $file
        ) {
            if (is_dir($file)) {
                $this->removeDirectory($file);
                continue;
            }

            unlink($file);
        }

        rmdir($directory);
    }

    public function test_generates_valid_repository_contract(): void
    {
        $generator = $this->createGenerator();

        $module = $this->createModuleData();

        $generator->generate($module);

        $file = $module->repositoryInterfacePath();

        $this->assertFileExists($file);

        $content = file_get_contents($file);

        $this->assertNotFalse($content);

        $this->assertStringContainsString(
            'interface CurrencyRepositoryInterface',
            $content
        );

        $this->assertStringContainsString(
            'Currency',
            $content
        );
    }

}
