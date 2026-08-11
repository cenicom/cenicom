<?php

declare(strict_types=1);

namespace Tests\Feature\Core\Security;

use App\Core\Security\Authorization\Contracts\AuthorizationServiceInterface;
use App\Core\Security\Authorization\Contracts\PermissionResolverInterface;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

final class AuthorizationPersistenceTest extends TestCase
{
    use RefreshDatabase;

    public function test_authorizes_direct_user_permission(): void
    {
        $user = User::factory()->create();

        $permission = Permission::create([
            'name' => 'users.view',
            'description' => 'View users',
            'module' => 'Users',
        ]);

        $user->permissions()->attach($permission);

        Auth::login($user);

        $identity = app(
            \App\Core\Security\Contracts\IdentityInterface::class
        );

        $authorization = app(
            AuthorizationServiceInterface::class
        );

        $this->assertTrue(
            $authorization->can(
                $identity,
                'users.view'
            )
        );
    }

    public function test_authorizes_role_permission(): void
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

        $identity = app(
            \App\Core\Security\Contracts\IdentityInterface::class
        );

        $authorization = app(
            AuthorizationServiceInterface::class
        );

        $this->assertTrue(
            $authorization->can(
                $identity,
                'users.delete'
            )
        );
    }

    public function test_denies_unknown_permission(): void
    {
        $user = User::factory()->create();

        Auth::login($user);

        $identity = app(
            \App\Core\Security\Contracts\IdentityInterface::class
        );

        $authorization = app(
            AuthorizationServiceInterface::class
        );

        $this->assertFalse(
            $authorization->can(
                $identity,
                'users.delete'
            )
        );
    }

    public function test_guest_cannot_authorize_permission(): void
    {
        $identity = app(
            \App\Core\Security\Contracts\IdentityInterface::class
        );

        $authorization = app(
            AuthorizationServiceInterface::class
        );

        $this->assertFalse(
            $authorization->can(
                $identity,
                'users.view'
            )
        );
    }

    public function test_direct_and_role_permissions_are_authorized(): void
    {
        $user = User::factory()->create();

        $role = Role::create([
            'name' => 'admin',
            'label' => 'Administrator',
        ]);

        $view = Permission::create([
            'name' => 'users.view',
            'description' => 'View users',
            'module' => 'Users',
        ]);

        $update = Permission::create([
            'name' => 'users.update',
            'description' => 'Update users',
            'module' => 'Users',
        ]);

        $delete = Permission::create([
            'name' => 'users.delete',
            'description' => 'Delete users',
            'module' => 'Users',
        ]);

        $user->permissions()->attach($view);

        $role->permissions()->attach([
            $update->id,
            $delete->id,
        ]);

        $user->roles()->attach($role);

        Auth::login($user);

        $identity = app(
            \App\Core\Security\Contracts\IdentityInterface::class
        );

        $authorization = app(
            AuthorizationServiceInterface::class
        );

        $this->assertTrue(
            $authorization->can(
                $identity,
                'users.view'
            )
        );

        $this->assertTrue(
            $authorization->can(
                $identity,
                'users.update'
            )
        );

        $this->assertTrue(
            $authorization->can(
                $identity,
                'users.delete'
            )
        );

        $this->assertFalse(
            $authorization->can(
                $identity,
                'users.create'
            )
        );
    }

    public function test_permission_resolver_uses_persistent_identity(): void
    {
        $user = User::factory()->create();

        $permission = Permission::create([
            'name' => 'institutions.view',
            'description' => 'View institutions',
            'module' => 'Institutions',
        ]);

        $user->permissions()->attach($permission);

        Auth::login($user);

        $identity = app(
            \App\Core\Security\Contracts\IdentityInterface::class
        );

        $resolver = app(
            PermissionResolverInterface::class
        );

        $this->assertTrue(
            $resolver->can(
                $identity,
                'institutions.view'
            )
        );

        $this->assertFalse(
            $resolver->can(
                $identity,
                'institutions.delete'
            )
        );
    }
}
