<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Security\Permissions;

use App\Core\Security\Permissions\DTO\PermissionDefinition;
use PHPUnit\Framework\TestCase;

final class PermissionDefinitionTest extends TestCase
{
    public function test_creates_permission_definition(): void
    {
        $permission = new PermissionDefinition(
            name: 'inventory.products.create',
            description: 'Allows creating products',
            module: 'Inventory',
        );

        $this->assertSame(
            'inventory.products.create',
            $permission->name
        );

        $this->assertSame(
            'Allows creating products',
            $permission->description
        );

        $this->assertSame(
            'Inventory',
            $permission->module
        );
    }


    public function test_returns_permission_key(): void
    {
        $permission = new PermissionDefinition(
            name: 'treasury.payments.approve'
        );

        $this->assertSame(
            'treasury.payments.approve',
            $permission->key()
        );
    }
}
