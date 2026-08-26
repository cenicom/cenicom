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
}
