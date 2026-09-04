<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Generator\Support\Permissions;

//use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\Support\Permissions\PermissionBuilder;
use Tests\Support\GeneratorTestCase;

final class PermissionBuilderTest extends GeneratorTestCase
{
    public function test_builds_expected_permission_variables(): void
    {
        $module = $this->createModuleData([
            'name' => 'Currency',
            'permissions' => true,
        ]);

        $builder = new PermissionBuilder();

        $variables = $builder->build($module);

        self::assertSame(
            $module->permissionNamespace(),
            $variables['namespace'],
        );

        self::assertSame(
            $module->name(),
            $variables['module'],
        );

        self::assertSame(
            $module->permissionClass(),
            $variables['moduleName'],
        );

        self::assertArrayHasKey('imports', $variables);
        self::assertArrayHasKey('constants', $variables);
        self::assertArrayHasKey('permissionDefinitions', $variables);
        self::assertArrayHasKey('permissionArray', $variables);
    }

    public function test_builds_expected_imports(): void
    {
        $module = $this->createModuleData([
            'name' => 'Currency',
            'permissions' => true,
        ]);

        $builder = new PermissionBuilder();

        $variables = $builder->build($module);

        self::assertStringContainsString(
            'PermissionDefinition',
            $variables['imports'],
        );
    }

    public function test_builds_permission_constants(): void
    {
        $module = $this->createModuleData([
            'name' => 'Currency',
            'permissions' => true,
        ]);

        $builder = new PermissionBuilder();

        $variables = $builder->build($module);

        self::assertIsString($variables['constants']);
    }

    public function test_builds_permission_definitions(): void
    {
        $module = $this->createModuleData([
            'name' => 'Currency',
            'permissions' => true,
        ]);

        $builder = new PermissionBuilder();

        $variables = $builder->build($module);

        self::assertIsString(
            $variables['permissionDefinitions'],
        );
    }

    public function test_builds_permission_array(): void
    {
        $module = $this->createModuleData([
            'name' => 'Currency',
            'permissions' => true,
        ]);

        $builder = new PermissionBuilder();

        $variables = $builder->build($module);

        self::assertIsString(
            $variables['permissionArray'],
        );
    }

    /**
     * Summary of test_preserves_plural_crud_permissions_from_module_data
     * @return void
     */
    public function test_preserves_plural_crud_permissions_from_module_data(): void
    {
        $module = $this->createModuleData([
            'identity' => [
                'name' => 'Currency',
                'singular' => 'currency',
                'plural' => 'currencies',
                'table' => 'currencies',
                'description' => 'Currency module',
            ],
            'permissions' => true,
        ]);

        $builder = new PermissionBuilder();

        $variables = $builder->build($module);

        self::assertStringContainsString(
            "public const VIEW = 'currencies.view';",
            $variables['constants'],
        );

        self::assertStringContainsString(
            "'permission' => 'currencies.view'",
            $variables['permissionDefinitions'],
        );

        self::assertStringContainsString(
            "'permission' => 'currencies.view'",
            $variables['permissionArray'],
        );

        self::assertStringContainsString(
            "public const CREATE = 'currencies.create';",
            $variables['constants'],
        );

        self::assertStringContainsString(
            "public const UPDATE = 'currencies.update';",
            $variables['constants'],
        );

        self::assertStringContainsString(
            "public const DELETE = 'currencies.delete';",
            $variables['constants'],
        );
    }
}
