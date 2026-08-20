<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Crud\Loader;

use App\Core\Crud\Loader\CrudDefinitionLoader;
use App\Core\Crud\Registry\CrudDefinitionRegistry;
use App\Core\Module\DTO\ModuleDefinition;
use App\Core\Module\Registry\ModuleRegistry;
use Tests\TestCase;

final class CrudDefinitionLoaderTest extends TestCase
{
    public function test_loads_crud_definitions_from_registered_modules(): void
    {
        $registry = new CrudDefinitionRegistry();

        $modules = new ModuleRegistry();

        $modules->register(
            new ModuleDefinition(
                name: 'Institution',
                namespace: 'App\\Modules\\Institution',
                basePath: '/tmp/institution',
                manifestPath: '/tmp/institution/module.php',
                providers: [],
                permissionDefinitions: [],
                navigationDefinitions: [],
                crudDefinitions: [
                    'Tests\\Fixtures\\Crud\\InstitutionCrudDefinition',
                ],
                viewDefinitions: [],
                enabled: true,
            )
        );

        $modules->register(
            new ModuleDefinition(
                name: 'Inventory',
                namespace: 'App\\Modules\\Inventory',
                basePath: '/tmp/inventory',
                manifestPath: '/tmp/inventory/module.php',
                providers: [],
                permissionDefinitions: [],
                navigationDefinitions: [],
                crudDefinitions: [
                    'Tests\\Fixtures\\Crud\\InventoryCrudDefinition',
                ],
                viewDefinitions: [],
                enabled: true,
            )
        );

        $loader = new CrudDefinitionLoader(
            $registry,
            $modules,
        );

        $loader->load();

        $this->assertSame(
            [
                'Tests\\Fixtures\\Crud\\InstitutionCrudDefinition',
                'Tests\\Fixtures\\Crud\\InventoryCrudDefinition',
            ],
            $registry->definitions()
        );
    }

    public function test_does_not_load_crud_definitions_from_disabled_modules(): void
    {
        $registry = new CrudDefinitionRegistry();

        $modules = new ModuleRegistry();

        $modules->register(
            new ModuleDefinition(
                name: 'Institution',
                namespace: 'App\\Modules\\Institution',
                basePath: '/tmp/institution',
                manifestPath: '/tmp/institution/module.php',
                providers: [],
                permissionDefinitions: [],
                navigationDefinitions: [],
                crudDefinitions: [
                    'Tests\\Fixtures\\Crud\\InstitutionCrudDefinition',
                ],
                viewDefinitions: [],
                enabled: false,
            )
        );

        $loader = new CrudDefinitionLoader(
            $registry,
            $modules,
        );

        $loader->load();

        $this->assertSame(
            [],
            $registry->definitions()
        );
    }

    public function test_does_nothing_when_modules_have_no_crud_definitions(): void
    {
        $registry = new CrudDefinitionRegistry();

        $modules = new ModuleRegistry();

        $modules->register(
            new ModuleDefinition(
                name: 'Institution',
                namespace: 'App\\Modules\\Institution',
                basePath: '/tmp/institution',
                manifestPath: '/tmp/institution/module.php',
                providers: [],
                permissionDefinitions: [],
                navigationDefinitions: [],
                crudDefinitions: [],
                viewDefinitions: [],
                enabled: true,
            )
        );

        $loader = new CrudDefinitionLoader(
            $registry,
            $modules,
        );

        $loader->load();

        $this->assertSame(
            [],
            $registry->definitions()
        );
    }

    public function test_does_nothing_when_no_modules_are_registered(): void
    {
        $registry = new CrudDefinitionRegistry();

        $modules = new ModuleRegistry();

        $loader = new CrudDefinitionLoader(
            $registry,
            $modules,
        );

        $loader->load();

        $this->assertSame(
            [],
            $registry->definitions()
        );
    }
}
