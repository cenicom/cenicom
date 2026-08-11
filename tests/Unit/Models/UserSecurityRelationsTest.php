<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Tests\TestCase;

final class UserSecurityRelationsTest extends TestCase
{
    public function test_roles_relation_is_belongs_to_many(): void
    {
        $user = new User();

        $relation = $user->roles();

        $this->assertInstanceOf(
            BelongsToMany::class,
            $relation
        );

        $this->assertSame(
            'role_user',
            $relation->getTable()
        );

        $this->assertSame(
            Role::class,
            $relation->getRelated()::class
        );
    }

    public function test_permissions_relation_is_belongs_to_many(): void
    {
        $user = new User();

        $relation = $user->permissions();

        $this->assertInstanceOf(
            BelongsToMany::class,
            $relation
        );

        $this->assertSame(
            'permission_user',
            $relation->getTable()
        );

        $this->assertSame(
            Permission::class,
            $relation->getRelated()::class
        );
    }

    public function test_user_does_not_define_currency_relation(): void
    {
        $user = new User();

        $this->assertFalse(
            method_exists($user, 'currency')
        );
    }

    public function test_security_relations_are_defined(): void
    {
        $user = new User();

        $this->assertTrue(
            method_exists($user, 'roles')
        );

        $this->assertTrue(
            method_exists($user, 'permissions')
        );
    }
}
