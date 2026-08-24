<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Navigation;

use App\Core\Module\DTO\ModuleDefinition;
use App\Core\Module\Registry\ModuleRegistry;
use App\Core\Navigation\Loader\NavigationDefinitionLoader;
use App\Core\Navigation\Registry\NavigationDefinitionRegistry;
use App\Modules\Institution\Navigation\InstitutionNavigation;
use App\Modules\Inventory\Navigation\InventoryNavigation;
use Tests\TestCase;

final class NavigationDefinitionLoaderTest extends TestCase
{
    public function test_loads_navigation_definitions_from_registered_modules(): void
    {
        // Arrange

        $registry = new NavigationDefinitionRegistry();

        $modules = new ModuleRegistry();

        $modules->register(
            new ModuleDefinition(
                name: 'Institution',
                namespace: 'App\\Modules\\Institution',
                basePath: '/tmp/institution',
                manifestPath: '/tmp/institution/module.php',
                providers: [],
                permissionDefinitions: [],
                navigationDefinitions: [
                    InstitutionNavigation::class,
                ],
                crudDefinitions: [],
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
                navigationDefinitions: [
                    InventoryNavigation::class,
                ],
                crudDefinitions: [],
                viewDefinitions: [],
                enabled: true,
            )
        );

        $loader = new NavigationDefinitionLoader(
            $registry,
            $modules,
        );

        // Act

        $loader->load();

        // Assert

        $this->assertSame(
            [
                InstitutionNavigation::class,
                InventoryNavigation::class,
            ],
            $registry->definitions()
        );
    }

    public function test_does_not_load_navigation_definitions_from_disabled_modules(): void
    {
        $registry = new NavigationDefinitionRegistry();

        $modules = new ModuleRegistry();

        $modules->register(
            new ModuleDefinition(
                name: 'Institution',
                namespace: 'App\\Modules\\Institution',
                basePath: '/tmp/institution',
                manifestPath: '/tmp/institution/module.php',
                providers: [],
                permissionDefinitions: [],
                navigationDefinitions: [
                    InstitutionNavigation::class,
                ],
                crudDefinitions: [],
                viewDefinitions: [],
                enabled: false,
            )
        );

        $loader = new NavigationDefinitionLoader(
            $registry,
            $modules,
        );

        $loader->load();

        $this->assertSame([], $registry->definitions());
    }

    public function test_load_does_nothing_when_modules_have_no_navigation_definitions(): void
    {
        $registry = new NavigationDefinitionRegistry();

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

        $loader = new NavigationDefinitionLoader(
            $registry,
            $modules,
        );

        $loader->load();

        $this->assertSame([], $registry->definitions());
    }
}
