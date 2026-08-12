<?php

declare(strict_types=1);

namespace Tests\Feature\Core\Security\Authorization;

use App\Core\Security\Authorization\AuthorizationAssignmentService;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class AuthorizationAuditIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_grant_permission_records_audit(): void
    {
        $user = User::factory()->create();

        $permission = Permission::query()->create([
            'name' => 'navigation.view',
        ]);

        $service = $this->app->make(
            AuthorizationAssignmentService::class
        );

        $service->grantPermission(
            $user,
            $permission,
        );

        $this->assertDatabaseHas('audit_entries', [
            'action' => 'authorization.changed',
            'subject_type' => 'user',
            'subject_id' => $user->getKey(),
            'result' => 'success',
        ]);
    }

    /* revokePermission() */
    public function test_revoke_permission_records_audit(): void
    {
        $user = User::factory()->create();

        $permission = Permission::query()->create([
            'name' => 'navigation.view',
        ]);

        $user->permissions()->attach(
            $permission->getKey(),
        );

        $service = $this->app->make(
            AuthorizationAssignmentService::class
        );

        $service->revokePermission(
            $user,
            $permission,
        );

        $this->assertDatabaseHas('audit_entries', [
            'action' => 'authorization.changed',
            'subject_type' => 'user',
            'subject_id' => $user->getKey(),
            'result' => 'success',
        ]);
    }

    /* assignRole() */
    public function test_assign_role_records_audit(): void
    {
        $user = User::factory()->create();

        $role = \App\Models\Role::query()->create([
            'name' => 'teacher',
            'label' => 'Teacher',
        ]);

        $service = $this->app->make(
            AuthorizationAssignmentService::class
        );

        $service->assignRole(
            $user,
            $role,
        );

        $this->assertDatabaseHas('audit_entries', [
            'action' => 'authorization.changed',
            'subject_type' => 'user',
            'subject_id' => $user->getKey(),
            'result' => 'success',
        ]);
    }

    /**
     * Summary of test_revoke_role_records_audit
     * revokeRole()
     * @return void
     */
    public function test_revoke_role_records_audit(): void
    {
        $user = User::factory()->create();

        $role = \App\Models\Role::query()->create([
            'name' => 'teacher',
            'label' => 'Teacher',
        ]);

        $user->roles()->attach(
            $role->getKey(),
        );

        $service = $this->app->make(
            AuthorizationAssignmentService::class
        );

        $service->revokeRole(
            $user,
            $role,
        );

        $this->assertDatabaseHas('audit_entries', [
            'action' => 'authorization.changed',
            'subject_type' => 'user',
            'subject_id' => $user->getKey(),
            'result' => 'success',
        ]);
    }

    /**
     * /⚓ Vamos con RED controlado: una operación real de CN-SEC sin actor autenticado
     * debe generar una entrada con actor System.
     * @return void
     */
    public function test_grant_permission_records_system_actor_when_unauthenticated(): void
    {
        $user = User::factory()->create();

        $permission = Permission::query()->create([
            'name' => 'navigation.view',
        ]);

        $service = $this->app->make(
            AuthorizationAssignmentService::class
        );

        $service->grantPermission(
            $user,
            $permission,
        );

        $this->assertDatabaseHas('audit_entries', [
            'action' => 'authorization.changed',
            'subject_type' => 'user',
            'subject_id' => $user->getKey(),
            'actor_id' => null,
            'actor_name' => 'Guest',
            'actor_authenticated' => false,
            'result' => 'success',
        ]);
    }
}
