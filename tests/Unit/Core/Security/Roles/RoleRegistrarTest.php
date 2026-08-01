<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Security\Roles;

use App\Core\Security\Roles\DTO\RoleDefinition;
use App\Core\Security\Roles\RoleRegistrar;
use App\Core\Security\Roles\RoleRegistry;
use PHPUnit\Framework\TestCase;

final class RoleRegistrarTest extends TestCase
{
    private RoleRegistry $registry;

    private RoleRegistrar $registrar;

    protected function setUp(): void
    {
        parent::setUp();

        $this->registry = new RoleRegistry();

        $this->registrar = new RoleRegistrar(
            $this->registry
        );
    }

    //1️⃣ registers role definition
    public function test_registers_role_definition(): void
    {
        $role = $this->registrar->register(
            name: 'administrator',
            label: 'Administrator'
        );

        $this->assertInstanceOf(
            RoleDefinition::class,
            $role
        );
    }

    //2️⃣ stores role in registry
    public function test_stores_role_in_registry(): void
    {
        $role = $this->registrar->register(
            name: 'administrator',
            label: 'Administrator'
        );

        $this->assertSame(
            $role,
            $this->registry->role('administrator')
        );
    }

    //3️⃣ returns created role
    public function test_returns_created_role(): void
    {
        $role = $this->registrar->register(
            name: 'teacher',
            label: 'Teacher'
        );

        $this->assertSame(
            'teacher',
            $role->name
        );

        $this->assertSame(
            'Teacher',
            $role->label
        );
    }

    //4️⃣ supports permission assignment
    public function test_supports_permission_assignment(): void
    {
        $permissions = [
            'users.create',
            'users.edit',
        ];

        $role = $this->registrar->register(
            name: 'administrator',
            label: 'Administrator',
            permissions: $permissions
        );

        $this->assertSame(
            $permissions,
            $role->permissions
        );
    }
}
