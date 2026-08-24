<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Security\Permissions;

use App\Core\Module\DTO\ModuleDefinition;
use App\Core\Module\Registry\ModuleRegistry;
use App\Core\Security\Permissions\Bootstrap\PermissionDefinitionBootstrapper;
use App\Core\Security\Permissions\Loader\PermissionDefinitionLoader;
use App\Core\Security\Permissions\PermissionDefinitionRegistry;
use App\Core\Security\Permissions\PermissionRegistrar;
use App\Core\Security\Permissions\PermissionRegistry;
use App\Modules\Institution\Security\InstitutionPermissionDefinition;
use App\Modules\Inventory\Security\InventoryPermissionDefinition;
use PHPUnit\Framework\TestCase;

final class PermissionDefinitionIntegrationTest extends TestCase
{
    public function test_load_and_bootstrap_registers_module_permissions(): void
    {
        $definitionRegistry = new PermissionDefinitionRegistry();

        $permissionRegistry = new PermissionRegistry();

        $registrar = new PermissionRegistrar(
            $permissionRegistry
        );

        $moduleRegistry = new ModuleRegistry();

        $moduleRegistry->register(
            new ModuleDefinition(
                name: 'Institution',
                namespace: 'App\\Modules\\Institution',
                basePath: '/tmp/institution',
                manifestPath: '/tmp/institution/module.php',
                providers: [],
                permissionDefinitions: [
                    InstitutionPermissionDefinition::class,
                ],
                navigationDefinitions: [],
                crudDefinitions: [],
                viewDefinitions: [],
                enabled: true,
            )
        );

        $moduleRegistry->register(
            new ModuleDefinition(
                name: 'Inventory',
                namespace: 'App\\Modules\\Inventory',
                basePath: '/tmp/inventory',
                manifestPath: '/tmp/inventory/module.php',
                providers: [],
                permissionDefinitions: [
                    InventoryPermissionDefinition::class,
                ],
                navigationDefinitions: [],
                crudDefinitions: [],
                viewDefinitions: [],
                enabled: true,
            )
        );

        $loader = new PermissionDefinitionLoader(
            $definitionRegistry,
            $moduleRegistry,
        );

        $bootstrapper = new PermissionDefinitionBootstrapper(
            $definitionRegistry,
            $registrar
        );

        $loader->load();

        self::assertSame(
            [
                InstitutionPermissionDefinition::class,
                InventoryPermissionDefinition::class,
            ],
            $definitionRegistry->definitions()
        );

        $bootstrapper->boot();

        // Institution
        self::assertNotNull(
            $permissionRegistry->permission(
                'institutions.view'
            )
        );

        self::assertNotNull(
            $permissionRegistry->permission(
                'institutions.create'
            )
        );

        self::assertNotNull(
            $permissionRegistry->permission(
                'institutions.update'
            )
        );

        self::assertNotNull(
            $permissionRegistry->permission(
                'institutions.delete'
            )
        );

        // Inventory
        self::assertNotNull(
            $permissionRegistry->permission(
                'inventory.products.view'
            )
        );

        self::assertNotNull(
            $permissionRegistry->permission(
                'inventory.categories.view'
            )
        );

        self::assertNotNull(
            $permissionRegistry->permission(
                'inventory.movements.view'
            )
        );

        self::assertCount(
            7,
            $permissionRegistry->all()
        );
    }
}
