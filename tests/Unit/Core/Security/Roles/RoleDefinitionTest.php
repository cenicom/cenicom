<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Security\Roles;

use App\Core\Security\Roles\DTO\RoleDefinition;
use PHPUnit\Framework\TestCase;

final class RoleDefinitionTest extends TestCase
{
    //1️⃣ creates role definition
    public function test_creates_role_definition(): void
    {
        $role = new RoleDefinition(
            name: 'administrator',
            label: 'Administrator',
            permissions: [
                'users.create',
                'users.edit',
            ]
        );

        $this->assertInstanceOf(
            RoleDefinition::class,
            $role
        );
    }

    //2️⃣ returns role name
    public function test_returns_role_name(): void
    {
        $role = new RoleDefinition(
            name: 'administrator',
            label: 'Administrator'
        );

        $this->assertSame(
            'administrator',
            $role->name
        );
    }

    //3️⃣ returns permissions
    public function test_returns_permissions(): void
    {
        $permissions = [
            'users.create',
            'users.edit',
        ];

        $role = new RoleDefinition(
            name: 'administrator',
            label: 'Administrator',
            permissions: $permissions
        );

        $this->assertSame(
            $permissions,
            $role->permissions
        );
    }

    //4️⃣ exports role as array
    public function test_exports_role_as_array(): void
{
    $role = new RoleDefinition(
        name: 'administrator',
        label: 'Administrator',
        permissions: [
            'users.create',
            'users.edit',
        ]
    );

    $this->assertSame(
        [
            'name' => 'administrator',
            'label' => 'Administrator',
            'permissions' => [
                'users.create',
                'users.edit',
            ],
        ],
        $role->toArray()
    );
}
}
