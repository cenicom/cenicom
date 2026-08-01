<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Security\Permissions;

use App\Core\Security\Permissions\DTO\PermissionDefinition;
use App\Core\Security\Permissions\PermissionRegistry;
use PHPUnit\Framework\TestCase;

final class PermissionRegistryTest extends TestCase
{
    public function test_registers_permission(): void
    {
        $registry = new PermissionRegistry();

        $permission = new PermissionDefinition(
            name: 'inventory.products.create'
        );

        $registry->register($permission);

        $this->assertSame(
            $permission,
            $registry->permission(
                'inventory.products.create'
            )
        );
    }


    public function test_retrieves_permission(): void
    {
        $registry = new PermissionRegistry();

        $registry->register(
            new PermissionDefinition(
                name: 'treasury.payments.approve'
            )
        );

        $permission = $registry->permission(
            'treasury.payments.approve'
        );

        $this->assertInstanceOf(
            PermissionDefinition::class,
            $permission
        );

        $this->assertSame(
            'treasury.payments.approve',
            $permission->key()
        );
    }


    public function test_returns_all_permissions(): void
    {
        $registry = new PermissionRegistry();

        $registry->register(
            new PermissionDefinition(
                name: 'inventory.products.view'
            )
        );

        $registry->register(
            new PermissionDefinition(
                name: 'inventory.products.create'
            )
        );

        $permissions = $registry->all();

        $this->assertCount(
            2,
            $permissions
        );

        $this->assertArrayHasKey(
            'inventory.products.view',
            $permissions
        );

        $this->assertArrayHasKey(
            'inventory.products.create',
            $permissions
        );
    }


    public function test_clears_registry(): void
    {
        $registry = new PermissionRegistry();

        $registry->register(
            new PermissionDefinition(
                name: 'inventory.products.delete'
            )
        );

        $registry->clear();

        $this->assertEmpty(
            $registry->all()
        );
    }


    public function test_replaces_duplicate_permission_key(): void
    {
        $registry = new PermissionRegistry();

        $first = new PermissionDefinition(
            name: 'inventory.products.update',
            description: 'First definition'
        );

        $second = new PermissionDefinition(
            name: 'inventory.products.update',
            description: 'Second definition'
        );

        $registry->register($first);
        $registry->register($second);

        $permission = $registry->permission(
            'inventory.products.update'
        );

        $this->assertSame(
            'Second definition',
            $permission?->description
        );
    }
}
