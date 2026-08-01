<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Security\Permissions;

use App\Core\Security\Permissions\DTO\PermissionDefinition;
use App\Core\Security\Permissions\PermissionRegistrar;
use App\Core\Security\Permissions\PermissionRegistry;
use PHPUnit\Framework\TestCase;

final class PermissionRegistrarTest extends TestCase
{
    private PermissionRegistry $registry;

    private PermissionRegistrar $registrar;


    protected function setUp(): void
    {
        parent::setUp();

        $this->registry = new PermissionRegistry();

        $this->registrar = new PermissionRegistrar(
            $this->registry
        );
    }


    public function test_registers_permission_definition(): void
    {
        $permission = $this->registrar->register(
            name: 'inventory.products.create'
        );

        $this->assertInstanceOf(
            PermissionDefinition::class,
            $permission
        );
    }


    public function test_stores_permission_in_registry(): void
    {
        $this->registrar->register(
            name: 'inventory.products.update'
        );

        $stored = $this->registry->permission(
            'inventory.products.update'
        );

        $this->assertInstanceOf(
            PermissionDefinition::class,
            $stored
        );

        $this->assertSame(
            'inventory.products.update',
            $stored->name
        );
    }


    public function test_returns_created_permission(): void
    {
        $permission = $this->registrar->register(
            name: 'inventory.products.delete'
        );

        $this->assertSame(
            'inventory.products.delete',
            $permission->key()
        );
    }


    public function test_supports_module_assignment(): void
    {
        $permission = $this->registrar->register(
            name: 'treasury.payments.approve',
            description: 'Approve payments',
            module: 'Treasury',
        );

        $this->assertSame(
            'Treasury',
            $permission->module
        );

        $this->assertSame(
            'Approve payments',
            $permission->description
        );
    }
}
