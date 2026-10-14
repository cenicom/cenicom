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

        $this->actingAs($user)
            ->get('/campuses/create')
            ->assertForbidden();
    }

    public function test_store_denies_authenticated_user_without_create_permission(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/campuses', [
                'institutionId' => '01J00000000000000000000000',
                'shortCode' => 'SEDEUNO',
                'name' => 'Sede de prueba',
                'address' => [
                    'country_id' => 1,
                    'state_id' => 1,
                    'city_id' => 1,
                ],
            ])
            ->assertForbidden();

        $this->assertDatabaseCount('campuses', 0);
        $this->assertDatabaseCount('addresses', 0);
    }

    public function test_create_allows_authenticated_user_with_create_permission(): void
    {
        $user = User::factory()->create();
        $this->grantPermission($user, 'campuses.create');

        $this->actingAs($user)
            ->get('/campuses/create')
            ->assertOk()
            ->assertViewIs('campuses::create');
    }

    public function test_store_rejects_missing_required_fields(): void
    {
        $user = User::factory()->create();
        $this->grantPermission($user, 'campuses.create');

        $this->actingAs($user)
            ->from('/campuses/create')
            ->post('/campuses', [])
            ->assertRedirect('/campuses/create')
            ->assertSessionHasErrors();

        $this->assertDatabaseCount('campuses', 0);
        $this->assertDatabaseCount('addresses', 0);
    }

    public function test_index_denies_authenticated_user_without_view_permission(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('campuses.index'))
            ->assertForbidden();
    }

    public function test_index_allows_user_with_view_permission(): void
    {
        $user = User::factory()->create();
        $this->grantPermission($user, 'campuses.view');

        $this->actingAs($user)
            ->get(route('campuses.index'))
            ->assertOk()
            ->assertSee('Sedes educativas')
            ->assertSee('No hay sedes registradas.');
    }

    public function test_index_hides_create_action_without_create_permission(): void
    {
        $user = User::factory()->create();
        $this->grantPermission($user, 'campuses.view');

        $this->actingAs($user)
            ->get(route('campuses.index'))
            ->assertOk()
            ->assertDontSee('Crear sede');
    }

    private function grantPermission(User $user, string $name): void
    {
        $permission = Permission::query()->firstOrCreate([
            'name' => $name,
        ]);

        $user->permissions()->attach($permission);
    }
}
