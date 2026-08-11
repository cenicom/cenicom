<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Tests\TestCase;

final class PermissionTest extends TestCase
{
    public function test_roles_relation_is_belongs_to_many(): void
    {
        $permission = new Permission();

        $relation = $permission->roles();

        $this->assertInstanceOf(
            BelongsToMany::class,
            $relation
        );

        $this->assertSame(
            'permission_role',
            $relation->getTable()
        );

        $this->assertSame(
            Role::class,
            $relation->getRelated()::class
        );
    }

    public function test_users_relation_is_belongs_to_many(): void
    {
        $permission = new Permission();

        $relation = $permission->users();

        $this->assertInstanceOf(
            BelongsToMany::class,
            $relation
        );

        $this->assertSame(
            'permission_user',
            $relation->getTable()
        );

        $this->assertSame(
            User::class,
            $relation->getRelated()::class
        );
    }

    public function test_fillable_attributes_are_defined(): void
    {
        $permission = new Permission();

        $this->assertSame(
            [
                'name',
                'description',
                'module',
            ],
            $permission->getFillable()
        );
    }
}
