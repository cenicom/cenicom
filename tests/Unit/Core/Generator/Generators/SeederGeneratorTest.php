<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Generator\Generators;

use App\Core\Generator\Generators\SeederGenerator;
use App\Core\Generator\Presentation\Factory\PresentationFactory;
use App\Core\Generator\Support\FileWriter;
use App\Core\Generator\Support\StubManager;
use App\Core\Generator\Validation\GeneratorValidator;
use Tests\Support\GeneratorTestCase;

final class SeederGeneratorTest extends GeneratorTestCase
{
    public function test_generates_seeder_file(): void
    {
        $generator = $this->createGenerator();

        $result = $generator->generate(
            $this->createModuleData()
        );

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

        $this->assertTrue(
            $generator->supports(
                $this->createModuleData()
            )
        );
    }

    public function test_generates_valid_seeder(): void
    {
        $generator = $this->createGenerator();

        $module = $this->createModuleData();

        $result = $generator->generate($module);

        $this->assertTrue(
            $result->isSuccessful()
        );

        $file = $module->seederPath();

        $this->assertFileExists($file);

        $content = file_get_contents($file);

        $this->assertNotFalse($content);

        $this->assertStringContainsString(
            'final class CurrencySeeder',
            $content
        );

        $this->assertStringContainsString(
            'extends Seeder',
            $content
        );

        $this->assertStringContainsString(
            'use App\\Models\\Currency;',
            $content
        );

        $this->assertStringContainsString(
            'Currency::factory()',
            $content
        );

        $this->assertStringContainsString(
            '->count(10)',
            $content
        );
    }

    private function createGenerator(): SeederGenerator
    {
        return new SeederGenerator(
            new StubManager(),
            new FileWriter(),
            new PresentationFactory(),
            new GeneratorValidator([]),
        );
    }
}
