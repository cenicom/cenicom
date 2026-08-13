<?php

declare(strict_types=1);

namespace Tests\Feature\Core\Security\Authorization;

use App\Core\Security\Authorization\AuthorizationAssignmentService;
use App\Core\Security\Authorization\Events\AuthorizationChanged;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

final class AuthorizationAssignmentServiceTest extends TestCase
{
    use RefreshDatabase;

    private AuthorizationAssignmentService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(
            AuthorizationAssignmentService::class
        );
    }

    public function test_assign_role_creates_user_role_assignment(): void
    {
        $user = $this->createUser();
        $role = $this->createRole('administrator');

        $this->service->assignRole(
            $user,
            $role
        );

        $this->assertTrue(
            $user->roles()
                ->whereKey($role->getKey())
                ->exists()
        );
    }

    public function test_assign_role_is_idempotent(): void
    {
        $user = $this->createUser();
        $role = $this->createRole('administrator');

        $this->service->assignRole($user, $role);
        $this->service->assignRole($user, $role);

        $this->assertSame(
            1,
            $user->roles()
                ->whereKey($role->getKey())
                ->count()
        );
    }

    public function test_revoke_role_removes_user_role_assignment(): void
    {
        $user = $this->createUser();
        $role = $this->createRole('administrator');

        $user->roles()->attach(
            $role->getKey()
        );

        $this->service->revokeRole(
            $user,
            $role
        );

        $this->assertFalse(
            $user->roles()
                ->whereKey($role->getKey())
                ->exists()
        );
    }

    public function test_grant_permission_creates_direct_user_permission_assignment(): void
    {
        $user = $this->createUser();
        $permission = $this->createPermission(
            'users.create'
        );

        $this->service->grantPermission(
            $user,
            $permission
        );

        $this->assertTrue(
            $user->permissions()
                ->whereKey($permission->getKey())
                ->exists()
        );
    }

    public function test_grant_permission_is_idempotent(): void
    {
        $user = $this->createUser();
        $permission = $this->createPermission(
            'users.create'
        );

        $this->service->grantPermission(
            $user,
            $permission
        );

        $this->service->grantPermission(
            $user,
            $permission
        );

        $this->assertSame(
            1,
            $user->permissions()
                ->whereKey($permission->getKey())
                ->count()
        );
    }

    public function test_revoke_permission_removes_direct_user_permission_assignment(): void
    {
        $user = $this->createUser();
        $permission = $this->createPermission(
            'users.create'
        );

        $user->permissions()->attach(
            $permission->getKey()
        );

        $this->service->revokePermission(
            $user,
            $permission
        );

        $this->assertFalse(
            $user->permissions()
                ->whereKey($permission->getKey())
                ->exists()
        );
    }

    public function test_grant_permission_to_role_creates_role_permission_assignment(): void
    {
        $role = $this->createRole('administrator');
        $permission = $this->createPermission(
            'users.create'
        );

        $this->service->grantPermissionToRole(
            $role,
            $permission
        );

        $this->assertTrue(
            $role->permissions()
                ->whereKey($permission->getKey())
                ->exists()
        );
    }

    public function test_grant_permission_to_role_is_idempotent(): void
    {
        $role = $this->createRole('administrator');
        $permission = $this->createPermission(
            'users.create'
        );

        $this->service->grantPermissionToRole(
            $role,
            $permission
        );

        $this->service->grantPermissionToRole(
            $role,
            $permission
        );

        $this->assertSame(
            1,
            $role->permissions()
                ->whereKey($permission->getKey())
                ->count()
        );
    }

    public function test_revoke_permission_from_role_removes_role_permission_assignment(): void
    {
        $role = $this->createRole('administrator');
        $permission = $this->createPermission(
            'users.create'
        );

        $role->permissions()->attach(
            $permission->getKey()
        );

        $this->service->revokePermissionFromRole(
            $role,
            $permission
        );

        $this->assertFalse(
            $role->permissions()
                ->whereKey($permission->getKey())
                ->exists()
        );
    }

    public function test_user_changes_dispatch_user_authorization_changed_event(): void
    {
        Event::fake([
            AuthorizationChanged::class,
        ]);

        $user = $this->createUser();
        $role = $this->createRole('administrator');

        $this->service->assignRole(
            $user,
            $role
        );

        Event::assertDispatched(
            AuthorizationChanged::class,
            function (AuthorizationChanged $event) use ($user): bool {
                return $event->scope === AuthorizationChanged::SCOPE_USER
                    && $event->identityId === $user->getAuthIdentifier()
                    && $event->role === null;
            }
        );
    }

    public function test_role_changes_dispatch_role_authorization_changed_event(): void
    {
        Event::fake([
            AuthorizationChanged::class,
        ]);

        $role = $this->createRole('administrator');
        $permission = $this->createPermission(
            'users.create'
        );

        $this->service->grantPermissionToRole(
            $role,
            $permission
        );

        Event::assertDispatched(
            AuthorizationChanged::class,
            function (AuthorizationChanged $event) use ($role): bool {
                return $event->scope === AuthorizationChanged::SCOPE_ROLE
                    && $event->identityId === null
                    && $event->role === $role->name;
            }
        );
    }

    private function createUser(): User
    {
        $unique = uniqid('', true);

        return User::query()->create([
            'user_code' => 'USR-AUTH-' . $unique,
            'user_name' => 'authorization-' . $unique,
            'first_name' => 'Authorization',
            'last_name' => 'Test',
            'email' => 'authorization-' . $unique . '@test.local',
            'password' => Hash::make(
                'password-' . $unique
            ),
        ]);
    }

    private function createRole(
        string $name
    ): Role {
        return Role::query()->create([
            'name' => $name,
            'label' => ucfirst($name),
        ]);
    }

    private function createPermission(
        string $name
    ): Permission {
        return Permission::query()->create([
            'name' => $name,
            'description' => 'Permission used by authorization tests.',
            'module' => 'Test',
        ]);
    }

    /**
     * Summary of test_authorization_changed_is_dispatched_after_commit
     * Paso 1 — prueba de la propiedad ShouldDispatchAfterCommit
     * @return void
     */
    public function test_authorization_changed_is_dispatched_after_commit(): void
    {
        $reflection = new \ReflectionClass(
            AuthorizationChanged::class
        );

        $this->assertTrue(
            $reflection->implementsInterface(
                \Illuminate\Contracts\Events\ShouldDispatchAfterCommit::class
            )
        );
    }
}
