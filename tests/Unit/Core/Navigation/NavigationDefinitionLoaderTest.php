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
}
