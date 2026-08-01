<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Security\Roles;

use App\Core\Security\Roles\DTO\RoleDefinition;
use App\Core\Security\Roles\RoleRegistry;
use PHPUnit\Framework\TestCase;

final class RoleRegistryTest extends TestCase
{
    private RoleRegistry $registry;

    protected function setUp(): void
    {
        parent::setUp();

        $this->registry = new RoleRegistry();
    }

    //1️⃣ registers role
    public function test_registers_role(): void
    {
        $role = new RoleDefinition(
            name: 'administrator',
            label: 'Administrator'
        );

        $this->registry->register($role);

        $this->assertSame(
            $role,
            $this->registry->role('administrator')
        );
    }

    //2️⃣ retrieves role
    public function test_retrieves_role(): void
    {
        $role = new RoleDefinition(
            name: 'teacher',
            label: 'Teacher'
        );

        $this->registry->register($role);

        $this->assertInstanceOf(
            RoleDefinition::class,
            $this->registry->role('teacher')
        );
    }

    //3️⃣ returns all roles
    public function test_returns_all_roles(): void
    {
        $this->registry->register(
            new RoleDefinition(
                name: 'administrator',
                label: 'Administrator'
            )
        );

        $this->registry->register(
            new RoleDefinition(
                name: 'teacher',
                label: 'Teacher'
            )
        );

        $roles = $this->registry->all();

        $this->assertCount(
            2,
            $roles
        );

        $this->assertArrayHasKey(
            'administrator',
            $roles
        );

        $this->assertArrayHasKey(
            'teacher',
            $roles
        );
    }

    //4️⃣ clears registry
    public function test_clears_registry(): void
    {
        $this->registry->register(
            new RoleDefinition(
                name: 'administrator',
                label: 'Administrator'
            )
        );

        $this->registry->clear();

        $this->assertSame(
            [],
            $this->registry->all()
        );
    }

    //5️⃣ replaces duplicate role name
    public function test_replaces_duplicate_role_name(): void
    {
        $this->registry->register(
            new RoleDefinition(
                name: 'administrator',
                label: 'Administrator'
            )
        );

        $updated = new RoleDefinition(
            name: 'administrator',
            label: 'System Administrator'
        );

        $this->registry->register($updated);

        $this->assertSame(
            $updated,
            $this->registry->role('administrator')
        );

        $this->assertCount(
            1,
            $this->registry->all()
        );
    }
}
