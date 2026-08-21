<?php

declare(strict_types=1);

namespace Tests\Unit\Core\View\Loader;

use App\Core\Module\DTO\ModuleDefinition;
use App\Core\Module\Registry\ModuleRegistry;
use App\Core\View\Loader\ViewDefinitionLoader;
use App\Core\View\Registry\ViewDefinitionRegistry;
use Tests\TestCase;

final class ViewDefinitionLoaderTest extends TestCase
{
    public function test_loads_view_definitions_from_registered_modules(): void
    {
        $registry = new ViewDefinitionRegistry();

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
                viewDefinitions: [
                    'App\\Modules\\Institution\\View\\InstitutionView',
                ],
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
                crudDefinitions: [],
                viewDefinitions: [
                    'App\\Modules\\Inventory\\View\\InventoryView',
                ],
                enabled: true,
            )
        );

        $loader = new ViewDefinitionLoader(
            $registry,
            $modules,
        );

        $loader->load();

        $this->assertSame(
            [
                'App\\Modules\\Institution\\View\\InstitutionView',
                'App\\Modules\\Inventory\\View\\InventoryView',
            ],
            $registry->definitions()
        );
    }

    public function test_does_not_load_view_definitions_from_disabled_modules(): void
    {
        $registry = new ViewDefinitionRegistry();

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
                viewDefinitions: [
                    'App\\Modules\\Institution\\View\\InstitutionView',
                ],
                enabled: false,
            )
        );

        $loader = new ViewDefinitionLoader(
            $registry,
            $modules,
        );

        $loader->load();

        $this->assertSame(
            [],
            $registry->definitions()
        );
    }

    public function test_does_nothing_when_modules_have_no_view_definitions(): void
    {
        $registry = new ViewDefinitionRegistry();

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

        $loader = new ViewDefinitionLoader(
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
        $registry = new ViewDefinitionRegistry();

        $modules = new ModuleRegistry();

        $loader = new ViewDefinitionLoader(
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
