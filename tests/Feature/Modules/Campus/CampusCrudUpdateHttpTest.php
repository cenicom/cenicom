<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\Campus;

use App\Models\Permission;
use App\Models\User;
use App\Modules\Address\Domain\Contracts\AddressServiceInterface;
use App\Modules\Campus\Models\Campus;
use App\Modules\Institution\Models\Institution;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

final class CampusCrudUpdateHttpTest extends TestCase
{
    private bool $mysqlTransactionStarted = false;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'mysql',
            'database.connections.mysql.database' => 'cenicom',
        ]);

        DB::purge('mysql');
        DB::setDefaultConnection('mysql');

        $this->assertSame(
            'cenicom',
            DB::connection('mysql')->getDatabaseName(),
            'Las pruebas de consulta y actualización deben usar la base cenicom.',
        );

        DB::connection('mysql')->beginTransaction();

        $this->mysqlTransactionStarted = true;
    }

    protected function tearDown(): void
    {
        try {
            if ($this->mysqlTransactionStarted) {
                DB::connection('mysql')->rollBack();

                $this->mysqlTransactionStarted = false;
            }
        } finally {
            parent::tearDown();
        }
    }

    public function test_show_denies_user_without_view_permission(): void
    {
        $campus = $this->createCampusForUpdate();
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('campuses.show', $campus))
            ->assertForbidden();
    }

    public function test_show_displays_campus_with_view_permission(): void
    {
        $campus = $this->createCampusForUpdate();
        $user = User::factory()->create();

        $this->grantPermission($user, 'campuses.view');

        $this->actingAs($user)
            ->get(route('campuses.show', $campus))
            ->assertOk()
            ->assertViewIs('campuses::show')
            ->assertSeeText($campus->name)
            ->assertSeeText('Carrera 10 # 20-30');
    }

    public function test_edit_denies_user_without_update_permission(): void
    {
        $campus = $this->createCampusForUpdate();
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('campuses.edit', $campus))
            ->assertForbidden();
    }

    public function test_edit_displays_form_with_update_permission(): void
    {
        $campus = $this->createCampusForUpdate();
        $user = User::factory()->create();

        $this->grantPermission($user, 'campuses.update');

        $this->actingAs($user)
            ->get(route('campuses.edit', $campus))
            ->assertOk()
            ->assertViewIs('campuses::edit')
            ->assertSeeText($campus->name)
            ->assertSee('name="name"', false)
            ->assertSee('name="ministryCode"', false);
    }

    public function test_update_denies_user_without_update_permission(): void
    {
        $campus = $this->createCampusForUpdate();
        $user = User::factory()->create();

        $originalName = $campus->name;
        $originalMinistryCode = $campus->ministry_code;

        $this->actingAs($user)
            ->put(route('campuses.update', $campus), $this->validUpdatePayload())
            ->assertForbidden();

        $this->assertDatabaseHas('campuses', [
            'id' => $campus->getKey(),
            'name' => $originalName,
            'ministry_code' => $originalMinistryCode,
        ], 'mysql');
    }

    public function test_update_persists_allowed_fields_and_ignores_identity_fields(): void
    {
        $campus = $this->createCampusForUpdate();
        $user = User::factory()->create();

        $this->grantPermission($user, 'campuses.update');

        $originalInstitutionId = $campus->institution_id;
        $originalCode = $campus->code;
        $originalShortCode = $campus->short_code;
        $originalStatus = $campus->status;
        $originalAddressId = $campus->address_id;

        $payload = array_merge(
            $this->validUpdatePayload(),
            [
                'institution_id' => (string) Str::ulid(),
                'code' => 'CEN-ATTACK-ATTACK-999',
                'short_code' => 'CAMBIADO123',
                'status' => 'active',
            ],
        );

        $this->actingAs($user)
            ->put(route('campuses.update', $campus), $payload)
            ->assertRedirect(route('campuses.show', $campus))
            ->assertSessionHas('success', 'Sede actualizada correctamente.');

        $this->assertDatabaseHas('campuses', [
            'id' => $campus->getKey(),
            'institution_id' => $originalInstitutionId,
            'code' => $originalCode,
            'short_code' => $originalShortCode,
            'name' => 'Sede actualizada',
            'ministry_code' => 'MIN-2026',
            'status' => $originalStatus,
            'address_id' => $originalAddressId,
        ], 'mysql');

        $this->assertDatabaseHas('addresses', [
            'id' => $originalAddressId,
            'country_id' => 1,
            'state_id' => 1,
            'city_id' => 1,
            'address' => 'Avenida 5 # 10-20',
            'neighborhood' => 'Nuevo Centro',
        ], 'mysql');
    }

    public function test_update_rejects_incoherent_geography_and_rolls_back_changes(): void
    {
        $campus = $this->createCampusForUpdate();
        $user = User::factory()->create();

        $this->grantPermission($user, 'campuses.update');

        $originalName = $campus->name;
        $originalMinistryCode = $campus->ministry_code;

        $originalAddress = $campus->address()->firstOrFail();

        $originalCountryId = $originalAddress->country_id;
        $originalStateId = $originalAddress->state_id;
        $originalCityId = $originalAddress->city_id;
        $originalAddressText = $originalAddress->address;
        $originalNeighborhood = $originalAddress->neighborhood;

        $payload = $this->validUpdatePayload();

        $payload['name'] = 'Este cambio debe revertirse';
        $payload['ministryCode'] = 'MIN-ROLLBACK';
        $payload['address']['city_id'] = 7;

        $this->actingAs($user)
            ->from(route('campuses.edit', $campus))
            ->put(route('campuses.update', $campus), $payload)
            ->assertRedirect(route('campuses.edit', $campus))
            ->assertSessionHasErrors('address.city_id');

        $this->assertDatabaseHas('campuses', [
            'id' => $campus->getKey(),
            'name' => $originalName,
            'ministry_code' => $originalMinistryCode,
        ], 'mysql');

        $this->assertDatabaseHas('addresses', [
            'id' => $originalAddress->getKey(),
            'country_id' => $originalCountryId,
            'state_id' => $originalStateId,
            'city_id' => $originalCityId,
            'address' => $originalAddressText,
            'neighborhood' => $originalNeighborhood,
        ], 'mysql');
    }

    private function validUpdatePayload(): array
    {
        return [
            'name' => 'Sede actualizada',
            'ministryCode' => 'MIN-2026',
            'address' => [
                'country_id' => 1,
                'state_id' => 1,
                'city_id' => 1,
                'address' => 'Avenida 5 # 10-20',
                'neighborhood' => 'Nuevo Centro',
            ],
        ];
    }

    private function grantPermission(User $user, string $name): void
    {
        $permission = Permission::query()->firstOrCreate([
            'name' => $name,
        ]);

        $user->permissions()->attach($permission);
    }

    private function createCampusForUpdate(): Campus
    {
        $institution = Institution::query()->create([
            'id' => (string) Str::ulid(),
            'code' => 'CEN' . Str::upper(Str::random(17)),
            'short_code' => 'IESB' . $this->randomLetters(8),
            'name' => 'Institución de prueba',
            'status' => 'draft',
        ]);

        $address = app(AddressServiceInterface::class)->create([
            'country_id' => 1,
            'state_id' => 1,
            'city_id' => 1,
            'address' => 'Carrera 10 # 20-30',
            'neighborhood' => 'Centro',
        ]);

        $campusShortCode = 'SEDE' . $this->randomLetters(8);

        return Campus::query()->create([
            'id' => (string) Str::ulid(),
            'institution_id' => $institution->getKey(),
            'code' => sprintf(
                'CEN-%s-%s-001',
                $institution->short_code,
                $campusShortCode,
            ),
            'short_code' => $campusShortCode,
            'name' => 'Sede de prueba',
            'ministry_code' => null,
            'address_id' => $address->getKey(),
            'status' => 'draft',
        ]);
    }

    private function randomLetters(int $length): string
    {
        return substr(
            str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'),
            0,
            $length,
        );
    }
}
