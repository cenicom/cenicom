<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\Institution;

use App\Models\Permission;
use App\Models\User;
//use App\Modules\Address\Domain\Contracts\AddressServiceInterface;
//use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

final class InstitutionCrudHttpTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_denies_authenticated_user_without_view_permission(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('institutions.index'));

        $response->assertForbidden();
    }

    public function test_index_allows_authenticated_user_with_view_permission(): void
    {
        $permission = Permission::query()->create([
            'name' => 'institutions.view',
        ]);

        $user = User::factory()->create();

        $user->permissions()->attach($permission);

        $response = $this
            ->actingAs($user)
            ->get(route('institutions.index'));

        $response->assertOk();
    }

    public function test_create_denies_authenticated_user_without_create_permission(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('institutions.create'));

        $response->assertForbidden();
    }

    public function test_create_allows_authenticated_user_with_create_permission(): void
    {
        $permission = Permission::query()->create([
            'name' => 'institutions.create',
        ]);

        $user = User::factory()->create();

        $user->permissions()->attach($permission);

        $response = $this
            ->actingAs($user)
            ->get(route('institutions.create'));

        $response
            ->assertOk()
            ->assertViewIs('institutions::create');
    }

    public function test_store_denies_authenticated_user_without_create_permission(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('institutions.store'), [
                'name' => 'Institución Educativa Simón Bolívar',
                'shortCode' => 'IESBSC',
            ]);

        $response->assertForbidden();

        $this->assertDatabaseMissing('institutions', [
            'name' => 'Institución Educativa Simón Bolívar',
        ]);
    }

    public function test_store_allows_authenticated_user_with_create_permission(): void
    {
        $permission = Permission::query()->create([
            'name' => 'institutions.create',
        ]);

        $user = User::factory()->create();

        $user->permissions()->attach($permission);

        $response = $this
            ->actingAs($user)
            ->post(route('institutions.store'), [
                'name' => 'Institución Educativa Simón Bolívar',
                'shortCode' => 'IESBSC',
                'officialRegistration' => [
                    'country' => 'CO',
                    'authority' => 'Ministerio de Educación',
                    'value' => 'REG-001',
                ],
            ]);

        $response
            ->assertRedirect(route('institutions.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('institutions', [
            'name' => 'Institución Educativa Simón Bolívar',
            'short_code' => 'IESBSC',
            'status' => 'draft',
        ]);
    }

    public function test_store_rejects_duplicate_short_code(): void
    {
        $permission = Permission::query()->create([
            'name' => 'institutions.create',
        ]);

        $user = User::factory()->create();
        $user->permissions()->attach($permission);

        $institution = \App\Modules\Institution\Models\Institution::query()->create([
            'id' => (string) Str::ulid(),
            'code' => 'CEN-IESB',
            'short_code' => 'IESBSC',
            'name' => 'Institución existente',
            'status' => 'draft',
        ]);

        $initialCount = \App\Modules\Institution\Models\Institution::query()->count();

        $response = $this
            ->actingAs($user)
            ->from(route('institutions.create'))
            ->post(route('institutions.store'), [
                'name' => 'Otra institución',
                'shortCode' => 'IESBSC',
            ]);

        $response
            ->assertRedirect(route('institutions.create'))
            ->assertSessionHasErrors(['shortCode']);

        $this->assertSame(
            $initialCount,
            \App\Modules\Institution\Models\Institution::query()->count(),
        );

        $this->assertDatabaseHas('institutions', [
            'id' => $institution->getKey(),
            'name' => 'Institución existente',
            'short_code' => 'IESBSC',
        ]);
    }

    public function test_edit_denies_authenticated_user_without_update_permission(): void
    {
        $user = User::factory()->create();

        $institution = \App\Modules\Institution\Models\Institution::query()->create([
            'id' => (string) Str::ulid(),
            'code' => 'CEN-IESB',
            'short_code' => 'IESBSC',
            'name' => 'Institución Educativa Simón Bolívar',
            'status' => 'draft',
        ]);

        $this->assertDatabaseHas('institutions', [
            'id' => $institution->getKey(),
        ]);

        $this->assertNotNull(
            $institution->resolveRouteBinding(
                $institution->getRouteKey(),
            ),
        );

        $response = $this
            ->actingAs($user)
            ->get(route('institutions.edit', $institution));

        $response->assertForbidden();
    }

    public function test_edit_allows_authenticated_user_with_update_permission(): void
    {
        $permission = Permission::query()->create([
            'name' => 'institutions.update',
        ]);

        $user = User::factory()->create();

        $user->permissions()->attach($permission);

        $institution = \App\Modules\Institution\Models\Institution::query()->create([
            'id' => (string) Str::ulid(),
            'code' => 'CEN-IESB',
            'short_code' => 'IESBSC',
            'name' => 'Institución Educativa Simón Bolívar',
            'status' => 'draft',
        ]);

        $this->assertDatabaseHas('institutions', [
            'id' => $institution->getKey(),
        ]);

        $this->assertNotNull(
            $institution->resolveRouteBinding(
                $institution->getRouteKey(),
            ),
        );

        $response = $this
            ->actingAs($user)
            ->get(route('institutions.edit', $institution));

        $response
            ->assertOk()
            ->assertViewIs('institutions::edit');
    }

    public function test_update_denies_authenticated_user_without_update_permission(): void
    {
        $user = User::factory()->create();

        $institution = \App\Modules\Institution\Models\Institution::query()->create([
            'id' => (string) Str::ulid(),
            'code' => 'CEN-IESB',
            'short_code' => 'IESBSC',
            'name' => 'Institución Educativa Simón Bolívar',
            'status' => 'draft',
        ]);

        $response = $this
            ->actingAs($user)
            ->put(
                route('institutions.update', $institution),
                [
                    'name' => 'Nombre no autorizado',
                ],
            );

        $response->assertForbidden();

        $this->assertDatabaseHas('institutions', [
            'id' => $institution->getKey(),
            'name' => 'Institución Educativa Simón Bolívar',
        ]);
    }

    public function test_update_allows_authenticated_user_with_update_permission(): void
    {
        $permission = Permission::query()->create([
            'name' => 'institutions.update',
        ]);

        $user = User::factory()->create();

        $user->permissions()->attach($permission);

        $institution = \App\Modules\Institution\Models\Institution::query()->create([
            'id' => (string) Str::ulid(),
            'code' => 'CEN-IESB',
            'short_code' => 'IESBSC',
            'name' => 'Institución Educativa Simón Bolívar',
            'status' => 'draft',
        ]);

        $response = $this
            ->actingAs($user)
            ->put(
                route('institutions.update', $institution),
                [
                    'name' => 'Institución Educativa Simón Bolívar Actualizada',
                ],
            );

        $response
            ->assertRedirect(route('institutions.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('institutions', [
            'id' => $institution->getKey(),
            'name' => 'Institución Educativa Simón Bolívar Actualizada',
            'short_code' => 'IESBSC',
            'status' => 'draft',
        ]);
    }


    public function test_update_preserves_official_registration_when_registration_is_omitted(): void
    {
        $permission = Permission::query()->create([
            'name' => 'institutions.update',
        ]);

        $user = User::factory()->create();

        $user->permissions()->attach($permission);

        $institution = \App\Modules\Institution\Models\Institution::query()->create([
            'id' => (string) Str::ulid(),
            'code' => 'CEN-IESB',
            'short_code' => 'IESBSC',
            'name' => 'Institución Educativa Simón Bolívar',
            'official_registration_country' => 'CO',
            'official_registration_authority' => 'Ministerio de Educación',
            'official_registration_value' => 'REG-000010',
            'status' => 'draft',
        ]);

        $response = $this
            ->actingAs($user)
            ->put(
                route('institutions.update', $institution),
                [
                    'name' => 'Institución Educativa Simón Bolívar Actualizada',
                ],
            );


        $response
            ->assertRedirect(route('institutions.index'))
            ->assertSessionHas('success');

        self::assertSame(302, $response->status());

        $this->assertDatabaseHas('institutions', [
            'id' => $institution->getKey(),
            'name' => 'Institución Educativa Simón Bolívar Actualizada',
            'official_registration_country' => 'CO',
            'official_registration_authority' => 'Ministerio de Educación',
            'official_registration_value' => 'REG-000010',
            'short_code' => 'IESBSC',
            'status' => 'draft',
        ]);
    }

    public function test_update_allows_empty_official_registration_when_form_submits_blank_fields(): void
    {
        $permission = Permission::query()->create([
            'name' => 'institutions.update',
        ]);

        $user = User::factory()->create();
        $user->permissions()->attach($permission);

        $institution = \App\Modules\Institution\Models\Institution::query()->create([
            'id' => (string) Str::ulid(),
            'code' => 'CEN-IESB',
            'short_code' => 'IESBSC',
            'name' => 'Institución Educativa Simón Bolívar',
            'status' => 'draft',
        ]);

        $response = $this
            ->actingAs($user)
            ->put(route('institutions.update', $institution), [
                'name' => 'Institución Educativa Simón Bolívar Actualizada',
                'officialRegistration' => [
                    'country' => '',
                    'authority' => '',
                    'value' => '',
                ],
            ]);

        $response
            ->assertRedirect(route('institutions.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('institutions', [
            'id' => $institution->getKey(),
            'name' => 'Institución Educativa Simón Bolívar Actualizada',
            'official_registration_country' => null,
            'official_registration_authority' => null,
            'official_registration_value' => null,
            'short_code' => 'IESBSC',
            'status' => 'draft',
        ]);
    }


    public function test_destroy_denies_authenticated_user_without_delete_permission(): void
    {
        $user = User::factory()->create();

        $institution = \App\Modules\Institution\Models\Institution::query()->create([
            'id' => (string) Str::ulid(),
            'code' => 'CEN-IESB',
            'short_code' => 'IESBSC',
            'name' => 'Institución Educativa Simón Bolívar',
            'status' => 'draft',
        ]);

        $response = $this
            ->actingAs($user)
            ->delete(
                route('institutions.destroy', $institution),
            );

        $response->assertForbidden();

        $this->assertDatabaseHas('institutions', [
            'id' => $institution->getKey(),
        ]);
    }

    public function test_destroy_allows_authenticated_user_with_delete_permission(): void
    {
        $permission = Permission::query()->create([
            'name' => 'institutions.delete',
        ]);

        $user = User::factory()->create();

        $user->permissions()->attach($permission);

        $institution = \App\Modules\Institution\Models\Institution::query()->create([
            'id' => (string) Str::ulid(),
            'code' => 'CEN-IESB',
            'short_code' => 'IESBSC',
            'name' => 'Institución Educativa Simón Bolívar',
            'status' => 'draft',
        ]);

        $response = $this
            ->actingAs($user)
            ->delete(
                route('institutions.destroy', $institution),
            );

        $response
            ->assertRedirect(route('institutions.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('institutions', [
            'id' => $institution->getKey(),
        ]);
    }


    public function test_destroy_blocks_institution_with_campus_code_sequence_dependency(): void
    {
        $permission = Permission::query()->create([
            'name' => 'institutions.delete',
        ]);

        $user = User::factory()->create();
        $user->permissions()->attach($permission);

        $institution = \App\Modules\Institution\Models\Institution::query()->create([
            'id' => (string) Str::ulid(),
            'code' => 'CEN-IESB',
            'short_code' => 'IESBSC',
            'name' => 'Institución Educativa Simón Bolívar',
            'status' => 'draft',
        ]);

        $now = now();

        \Illuminate\Support\Facades\DB::table('campus_code_sequences')->insert([
            'institution_id' => $institution->getKey(),
            'current_value' => 0,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $this->assertDatabaseHas('campus_code_sequences', [
            'institution_id' => $institution->getKey(),
        ]);

        $response = $this
            ->actingAs($user)
            ->delete(route('institutions.destroy', $institution));

        $response
            ->assertRedirect(route('institutions.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('institutions', [
            'id' => $institution->getKey(),
        ]);

        $this->assertDatabaseHas('campus_code_sequences', [
            'institution_id' => $institution->getKey(),
        ]);
    }
}
