<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Security\Permissions\Loader;

use App\Core\Contracts\Module\ModuleRegistryInterface;
use App\Core\Module\DTO\ModuleDefinition;
use App\Core\Security\Permissions\Contracts\PermissionDefinitionRegistryInterface;
use App\Core\Security\Permissions\Loader\PermissionDefinitionLoader;
use PHPUnit\Framework\TestCase;

final class PermissionDefinitionLoaderTest extends TestCase
{
    public function test_load_registers_permission_definitions_from_registered_modules(): void
    {
        $registry = $this->createMock(
            PermissionDefinitionRegistryInterface::class
        );

        $modules = $this->createMock(
            ModuleRegistryInterface::class
        );

        $definitions = [
            'Tests\\Fixtures\\Permissions\\FirstPermissionDefinition',
            'Tests\\Fixtures\\Permissions\\SecondPermissionDefinition',
        ];

        $module = new ModuleDefinition(
            name: 'TestModule',
            namespace: 'Tests\\Fixtures\\Modules\\TestModule',
            basePath: '/tmp/test-module',
            manifestPath: '/tmp/test-module/module.php',
            providers: [],
            permissionDefinitions: $definitions,
            navigationDefinitions: [],
            crudDefinitions: [],
            viewDefinitions: [],
            enabled: true,
        );

        $modules
            ->expects(self::once())
            ->method('all')
            ->willReturn([$module]);

        $registered = [];

        $registry
            ->expects(self::exactly(2))
            ->method('add')
            ->willReturnCallback(
                function (string $definition) use (&$registered): void {
                    $registered[] = $definition;
                }
            );

        $loader = new PermissionDefinitionLoader(
            $registry,
            $modules,
        );

        $loader->load();

        self::assertSame(
            $definitions,
            $registered
        );
    }

    public function test_load_registers_definitions_from_multiple_modules(): void
    {
        $registry = $this->createMock(
            PermissionDefinitionRegistryInterface::class
        );

        $modules = $this->createMock(
            ModuleRegistryInterface::class
        );

        $firstDefinition =
            'Tests\\Fixtures\\Permissions\\FirstPermissionDefinition';

        $secondDefinition =
            'Tests\\Fixtures\\Permissions\\SecondPermissionDefinition';

        $firstModule = new ModuleDefinition(
            name: 'FirstModule',
            namespace: 'Tests\\Fixtures\\Modules\\FirstModule',
            basePath: '/tmp/first-module',
            manifestPath: '/tmp/first-module/module.php',
            providers: [],
            permissionDefinitions: [$firstDefinition],
            navigationDefinitions: [],
            crudDefinitions: [],
            viewDefinitions: [],
            enabled: true,
        );

        $secondModule = new ModuleDefinition(
            name: 'SecondModule',
            namespace: 'Tests\\Fixtures\\Modules\\SecondModule',
            basePath: '/tmp/second-module',
            manifestPath: '/tmp/second-module/module.php',
            providers: [],
            permissionDefinitions: [$secondDefinition],
            navigationDefinitions: [],
            crudDefinitions: [],
            viewDefinitions: [],
            enabled: true,
        );

        $modules
            ->expects(self::once())
            ->method('all')
            ->willReturn([
                $firstModule,
                $secondModule,
            ]);

        $registered = [];

        $registry
            ->expects(self::exactly(2))
            ->method('add')
            ->willReturnCallback(
                function (string $definition) use (&$registered): void {
                    $registered[] = $definition;
                }
            );

        $loader = new PermissionDefinitionLoader(
            $registry,
            $modules,
        );

        $loader->load();

        self::assertSame(
            [
                $firstDefinition,
                $secondDefinition,
            ],
            $registered
        );
    }

    public function test_load_does_nothing_when_no_modules_are_registered(): void
    {
        $registry = $this->createMock(
            PermissionDefinitionRegistryInterface::class
        );

        $modules = $this->createMock(
            ModuleRegistryInterface::class
        );

        $modules
            ->expects(self::once())
            ->method('all')
            ->willReturn([]);

        $registry
            ->expects(self::never())
            ->method('add');

        $loader = new PermissionDefinitionLoader(
            $registry,
            $modules,
        );

        $loader->load();
    }
}
