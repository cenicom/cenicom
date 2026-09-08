<?php

declare(strict_types=1);

namespace Tests\Feature\Core\Security;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

final class PermissionMiddlewareHttpTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Route::get('/__test/permission', fn() => response('ok'))
            ->middleware('permission:institutions.view');
    }

    public function test_unauthenticated_user_is_denied(): void
    {
        $response = $this->get('/__test/permission');

        $response->assertForbidden();
    }

    public function test_authenticated_user_without_permission_is_denied(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/__test/permission');

        $response->assertForbidden();
    }

    public function test_authenticated_user_with_permission_is_allowed(): void
    {
        $permission = Permission::query()->create([
            'name' => 'institutions.view',
        ]);

        $role = Role::query()->create([
            'name' => 'administrator',
            'label' => 'Administrator',
        ]);

        $role->permissions()->attach($permission);

        $user = User::factory()->create();

        $user->roles()->attach($role);

        $response = $this
            ->actingAs($user)
            ->get('/__test/permission');

        $response
            ->assertOk()
            ->assertSee('ok');
    }

    public function test_authenticated_user_with_direct_permission_is_allowed(): void
    {
        $permission = Permission::query()->create([
            'name' => 'institutions.view',
        ]);

        $user = User::factory()->create();

        $user->permissions()->attach($permission);

        $response = $this
            ->actingAs($user)
            ->get('/__test/permission');

        $response
            ->assertOk()
            ->assertSee('ok');
    }

    public function test_authenticated_user_with_any_matching_permission_is_allowed(): void
    {
        Route::get('/__test/permission-any', fn() => response('ok'))
            ->middleware('permission:institutions.view|institutions.admin');

        $permission = Permission::query()->create([
            'name' => 'institutions.admin',
        ]);

        $user = User::factory()->create();

        $user->permissions()->attach($permission);

        $response = $this
            ->actingAs($user)
            ->get('/__test/permission-any');

        $response
            ->assertOk()
            ->assertSee('ok');
    }

    public function test_authenticated_user_without_any_required_permission_is_denied(): void
    {
        Route::get('/__test/permission-any-denied', fn() => response('ok'))
            ->middleware('permission:institutions.view|institutions.admin');

        $permission = Permission::query()->create([
            'name' => 'institutions.other',
        ]);

        $user = User::factory()->create();

        $user->permissions()->attach($permission);

        $response = $this
            ->actingAs($user)
            ->get('/__test/permission-any-denied');

        $response->assertForbidden();
    }
}
