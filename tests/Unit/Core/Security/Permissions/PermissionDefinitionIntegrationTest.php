<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Security\Permissions;

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

        $loader = new PermissionDefinitionLoader(
            $definitionRegistry
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
