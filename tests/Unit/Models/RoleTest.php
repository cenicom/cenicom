<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Tests\TestCase;

final class RoleTest extends TestCase
{
    public function test_users_relation_is_belongs_to_many(): void
    {
        $role = new Role();

        $relation = $role->users();

        $this->assertInstanceOf(
            BelongsToMany::class,
            $relation
        );

        $this->assertSame(
            'role_user',
            $relation->getTable()
        );

        $this->assertSame(
            User::class,
            $relation->getRelated()::class
        );
    }

    public function test_permissions_relation_is_belongs_to_many(): void
    {
        $role = new Role();

        $relation = $role->permissions();

        $this->assertInstanceOf(
            BelongsToMany::class,
            $relation
        );

        $this->assertSame(
            'permission_role',
            $relation->getTable()
        );

        $this->assertSame(
            Permission::class,
            $relation->getRelated()::class
        );
    }

    public function test_fillable_attributes_are_defined(): void
    {
        $role = new Role();

        $this->assertSame(
            [
                'name',
                'label',
            ],
            $role->getFillable()
        );
    }
}
