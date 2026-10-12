<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\Campus;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class CampusCrudHttpTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_denies_authenticated_user_without_create_permission(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/campuses/create');

        $response->assertForbidden();
    }

    public function test_store_denies_authenticated_user_without_create_permission(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post('/campuses', [
                'institutionId' => '01J00000000000000000000000',
                'shortCode' => 'SEDEUNO',
                'name' => 'Sede de prueba',
                'address' => [
                    'country_id' => 1,
                    'state_id' => 1,
                    'city_id' => 1,
                ],
            ]);

        $response->assertForbidden();

        $this->assertDatabaseCount('campuses', 0);
        $this->assertDatabaseCount('addresses', 0);
    }

    public function test_create_allows_authenticated_user_with_create_permission(): void
    {
        Permission::query()->create([
            'name' => 'campuses.create',
        ]);

        $permission = Permission::query()
            ->where('name', 'campuses.create')
            ->firstOrFail();

        $user = User::factory()->create();
        $user->permissions()->attach($permission);

        $response = $this
            ->actingAs($user)
            ->get('/campuses/create');

        $response->assertOk();
    }

    public function test_store_rejects_missing_required_fields(): void
    {
        Permission::query()->create([
            'name' => 'campuses.create',
        ]);

        $permission = Permission::query()
            ->where('name', 'campuses.create')
            ->firstOrFail();

        $user = User::factory()->create();
        $user->permissions()->attach($permission);

        $response = $this
            ->actingAs($user)
            ->from('/campuses/create')
            ->post('/campuses', []);

        $response
            ->assertRedirect('/campuses/create')
            ->assertSessionHasErrors();

        $this->assertDatabaseCount('campuses', 0);
        $this->assertDatabaseCount('addresses', 0);
    }

    public function test_index_denies_authenticated_user_without_view_permission(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('campuses.index'));

        $response->assertForbidden();
    }

    public function test_index_allows_user_with_view_permission(): void
    {
        Permission::query()->create([
            'name' => 'campuses.view',
        ]);

        $permission = Permission::query()
            ->where('name', 'campuses.view')
            ->firstOrFail();

        $user = User::factory()->create();
        $user->permissions()->attach($permission);

        $response = $this
            ->actingAs($user)
            ->get(route('campuses.index'));

        $response
            ->assertOk()
            ->assertSee('Sedes educativas')
            ->assertSee('No hay sedes registradas.');
    }

    public function test_index_hides_create_action_without_create_permission(): void
    {
        Permission::query()->create([
            'name' => 'campuses.view',
        ]);

        $permission = Permission::query()
            ->where('name', 'campuses.view')
            ->firstOrFail();

        $user = User::factory()->create();
        $user->permissions()->attach($permission);

        $response = $this
            ->actingAs($user)
            ->get(route('campuses.index'));

        $response
            ->assertOk()
            ->assertDontSee('Crear sede');
    }
}
