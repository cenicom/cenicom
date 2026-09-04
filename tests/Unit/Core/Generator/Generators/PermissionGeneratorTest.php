<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Generator\Generators;

use App\Core\Generator\Generators\PermissionGenerator;
use App\Core\Generator\Support\FileWriter;
use App\Core\Generator\Support\Permissions\PermissionBuilder;
use App\Core\Generator\Support\StubManager;
use App\Core\Generator\Presentation\Factory\PresentationFactory;
use App\Core\Generator\Validation\GeneratorValidator;
use Tests\Support\GeneratorTestCase;

final class PermissionGeneratorTest extends GeneratorTestCase
{
    public function test_generates_permission_file(): void
    {
        $module = $this->createModuleData([
            'identity' => [
                'name' => 'Currency',
            ],
            'generation' => [
                'permissions' => true,
            ],
        ]);

        $generator = $this->createGenerator();

        $result = $generator->generate($module);

        self::assertTrue(
            $result->isSuccessful(),
        );

        self::assertFileExists(
            $module->permissionPath(),
        );

        self::assertContains(
            $module->permissionPath(),
            $result->created(),
        );
    }

    public function test_generator_supports_module_with_permissions(): void
    {
        $module = $this->createModuleData([
            'identity' => [
                'name' => 'Currency',
            ],
            'generation' => [
                'permissions' => true,
            ],
        ]);

        $generator = $this->createGenerator();

        self::assertTrue(
            $generator->supports($module),
        );
    }

    public function test_generator_does_not_support_module_without_permissions(): void
    {
        $module =  $this->createModuleData([
            'generation' => [
                'permissions' => false,
            ],
        ]);

        $generator = $this->createGenerator();

        self::assertFalse(
            $generator->supports($module),
        );
    }

    public function test_generates_valid_permission(): void
    {
        $module = $this->createModuleData([
            'identity' => [
                'name' => 'Currency',
                'singular' => 'currency',
                'plural' => 'currencies',
                'table' => 'currencies',
                'description' => 'Currency module',
            ],
            'generation' => [
                'permissions' => true,
            ],
        ]);

        $generator = $this->createGenerator();

        $result = $generator->generate($module);

        self::assertTrue(
            $result->isSuccessful(),
        );

        $file = $module->permissionPath();

        self::assertFileExists($file);

        $content = file_get_contents($file);

        self::assertNotFalse($content);

        self::assertStringContainsString(
            'final class CurrencyPermissions',
            $content,
        );

        self::assertStringContainsString(
            'definitions(): array',
            $content,
        );

        self::assertStringContainsString(
            'toArray(): array',
            $content,
        );

        self::assertStringContainsString(
            "public const VIEW = 'currencies.view';",
            $content,
        );

        self::assertStringContainsString(
            "public const CREATE = 'currencies.create';",
            $content,
        );

        self::assertStringContainsString(
            "public const UPDATE = 'currencies.update';",
            $content,
        );

        self::assertStringContainsString(
            "public const DELETE = 'currencies.delete';",
            $content,
        );

        self::assertStringContainsString(
            "'permission' => 'currencies.view'",
            $content,
        );

        self::assertStringContainsString(
            "'permission' => 'currencies.create'",
            $content,
        );

        self::assertStringContainsString(
            "'permission' => 'currencies.update'",
            $content,
        );

        self::assertStringContainsString(
            "'permission' => 'currencies.delete'",
            $content,
        );
    }

    private function createGenerator(): PermissionGenerator
    {
        return new PermissionGenerator(
            new StubManager(
                resource_path('stubs'),
            ),
            new FileWriter(),
            new PresentationFactory(),
            new GeneratorValidator([]),
            new PermissionBuilder(),
        );
    }
}
