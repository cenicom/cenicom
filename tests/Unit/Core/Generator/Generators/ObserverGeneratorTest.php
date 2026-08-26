<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Generator\Generators;

//use App\Core\Generator\Builders\RouteBuilder;
use App\Core\Generator\Generators\ObserverGenerator;
use App\Core\Generator\Presentation\Factory\PresentationFactory;
use App\Core\Generator\Support\FileWriter;
use App\Core\Generator\Support\Observer\ObserverBuilder;
use App\Core\Generator\Support\StubManager;
use App\Core\Generator\Validation\GeneratorValidator;
use Tests\Support\GeneratorTestCase;

final class ObserverGeneratorTest extends GeneratorTestCase
{
    public function test_generates_observer_file(): void
    {
        $module = $this->createModuleData();

        $generator = $this->createGenerator();

        $result = $generator->generate($module);

        self::assertTrue(
            $result->isSuccessful(),
        );

        self::assertFileExists(
            $module->observerPath(),
        );
    }

    public function test_generator_supports_any_module(): void
    {
        $generator = $this->createGenerator();

        self::assertTrue(
            $generator->supports(
                $this->createModuleData(),
            ),
        );
    }

    public function test_generates_valid_observer(): void
    {
        $module = $this->createModuleData();

        $generator = $this->createGenerator();

        $generator->generate($module);

        $path = $module->observerPath();

        self::assertFileExists($path);

        $content = file_get_contents($path);

        self::assertNotFalse($content);

        self::assertStringContainsString(
            "namespace {$module->observerNamespace()};",
            $content,
        );

        self::assertStringContainsString(
            "final class {$module->observerClass()}",
            $content,
        );

        self::assertStringContainsString(
            "use {$module->qualifiedModel()};",
            $content,
        );

        foreach (
            [
                'creating',
                'created',
                'updating',
                'updated',
                'deleting',
                'deleted',
                'restoring',
                'restored',
                'forceDeleted',
            ] as $method
        ) {
            self::assertStringContainsString(
                "public function {$method}(",
                $content,
            );
        }
    }

    private function createGenerator(): ObserverGenerator
    {
        return new ObserverGenerator(
            app(StubManager::class),
            app(FileWriter::class),
            app(PresentationFactory::class),
            app(GeneratorValidator::class),
            app(ObserverBuilder::class),
        );
    }
}
