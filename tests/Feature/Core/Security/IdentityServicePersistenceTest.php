<?php

declare(strict_types=1);

namespace Tests\Feature\Core\Security;

use App\Core\Security\DTO\IdentityData;
use App\Core\Security\Services\IdentityService;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

final class IdentityServicePersistenceTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_guest_when_no_user_is_authenticated(): void
    {
        $service = app(IdentityService::class);

        $identity = $service->current();

        $this->assertInstanceOf(
            IdentityData::class,
            $identity
        );

        $this->assertNull(
            $identity->id
        );

        $this->assertFalse(
            $identity->authenticated
        );

        $this->assertSame(
            'Guest',
            $identity->name
        );
    }

    public function test_resolves_authenticated_user_from_database(): void
    {
        $user = User::factory()->create([
            'first_name' => 'Administrator',
            'last_name' => 'User',
        ]);

        Auth::login($user);

        $service = app(IdentityService::class);

        $identity = $service->current();

        $this->assertSame(
            $user->getAuthIdentifier(),
            $identity->id
        );

        $this->assertSame(
            'Administrator User',
            $identity->name
        );

        $this->assertTrue(
            $identity->authenticated
        );
    }

    public function test_resolves_roles_from_database(): void
    {
        $user = User::factory()->create();

        $role = Role::create([
            'name' => 'admin',
            'label' => 'Administrator',
        ]);

        $user->roles()->attach($role);

        Auth::login($user);

        $identity = app(IdentityService::class)->current();

        $this->assertSame(
            ['admin'],
            $identity->roles
        );
    }

    public function test_resolves_direct_permissions_from_database(): void
    {
        $user = User::factory()->create();

        $permission = Permission::create([
            'name' => 'users.view',
            'description' => 'View users',
            'module' => 'Users',
        ]);

        $user->permissions()->attach($permission);

        Auth::login($user);

        $identity = app(IdentityService::class)->current();

        $this->assertContains(
            'users.view',
            $identity->permissions
        );
    }

    public function test_resolves_permissions_inherited_from_roles(): void
    {
        $user = User::factory()->create();

        $role = Role::create([
            'name' => 'admin',
            'label' => 'Administrator',
        ]);

        $permission = Permission::create([
            'name' => 'users.delete',
            'description' => 'Delete users',
            'module' => 'Users',
        ]);

        $role->permissions()->attach($permission);
        $user->roles()->attach($role);

        Auth::login($user);

        $identity = app(IdentityService::class)->current();

        $this->assertContains(
            'users.delete',
            $identity->permissions
        );
    }

    public function test_merges_direct_and_role_permissions_without_duplicates(): void
    {
        $user = User::factory()->create();

        $role = Role::create([
            'name' => 'admin',
            'label' => 'Administrator',
        ]);

        $directPermission = Permission::create([
            'name' => 'users.view',
            'description' => 'View users',
            'module' => 'Users',
        ]);

        $rolePermission = Permission::create([
            'name' => 'users.delete',
            'description' => 'Delete users',
            'module' => 'Users',
        ]);

        $sharedPermission = Permission::create([
            'name' => 'users.update',
            'description' => 'Update users',
            'module' => 'Users',
        ]);

        $user->permissions()->attach([
            $directPermission->id,
            $sharedPermission->id,
        ]);

        $role->permissions()->attach([
            $rolePermission->id,
            $sharedPermission->id,
        ]);

        $user->roles()->attach($role);

        Auth::login($user);

        $identity = app(IdentityService::class)->current();

        $this->assertSame(
            3,
            count($identity->permissions)
        );

        $this->assertSame(
            [
                'users.view',
                'users.update',
                'users.delete',
            ],
            $identity->permissions
        );
    }
}
