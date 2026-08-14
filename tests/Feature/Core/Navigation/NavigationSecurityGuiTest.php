<?php

declare(strict_types=1);

namespace Tests\Feature\Core\Navigation;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class NavigationSecurityGuiTest extends TestCase
{
    use RefreshDatabase;

    public function test_authorized_user_sees_institution_navigation_in_gui(): void
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
            ->get('/');

        $response
            ->assertOk()
            ->assertSee('Instituciones');
    }

    public function test_unauthorized_user_does_not_see_institution_navigation_in_gui(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/');

        $response
            ->assertOk()
            ->assertDontSee('Instituciones');
    }
}
