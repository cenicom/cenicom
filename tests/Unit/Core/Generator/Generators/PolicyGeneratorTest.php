<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Generator\Generators;


use App\Core\Generator\Generators\PolicyGenerator;
use App\Core\Generator\Presentation\Factory\PresentationFactory;
use App\Core\Generator\Support\FileWriter;
use App\Core\Generator\Support\Policies\PolicyBuilder;
use App\Core\Generator\Support\StubManager;
use App\Core\Generator\Validation\GeneratorValidator;
use Tests\Support\GeneratorTestCase;

final class PolicyGeneratorTest extends GeneratorTestCase
{
    public function test_generates_policy_file(): void
    {
        $module = $this->createModuleData();

        $generator = $this->createGenerator();

        $generator->generate($module);

        self::assertFileExists(
            $module->policyPath(),
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

    public function test_generates_valid_policy(): void
    {
        $module = $this->createModuleData();

        $generator = $this->createGenerator();

        $generator->generate($module);

        $file = $module->policyPath();

        self::assertFileExists($file);

        $content = file_get_contents($file);

        self::assertNotFalse($content);

        self::assertStringContainsString(
            "namespace {$module->policyNamespace()};",
            $content,
        );

        self::assertStringContainsString(
            "final class {$module->policyClass()}",
            $content,
        );

        self::assertStringContainsString(
            'use App\Models\User;',
            $content,
        );

        self::assertStringContainsString(
            sprintf(
                'use %s\\%s;',
                $module->modelNamespace(),
                $module->modelClass(),
            ),
            $content,
        );

        foreach (
            [
                'viewAny',
                'view',
                'create',
                'update',
                'delete',
                'restore',
                'forceDelete',
            ] as $method
        ) {
            self::assertStringContainsString(
                "public function {$method}",
                $content,
            );
        }

        self::assertStringContainsString(
            "{$module->modelClass()} \${$module->variable()}",
            $content,
        );
    }

    private function createGenerator(): PolicyGenerator
    {
        return new PolicyGenerator(
            new StubManager(),
            new FileWriter(),
            app(PresentationFactory::class),
            app(GeneratorValidator::class),
            new PolicyBuilder(),
        );
    }
}
