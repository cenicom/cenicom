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

final class CampusCrudDeletionHttpTest extends TestCase
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
            'Las pruebas de eliminación deben usar la base cenicom.',
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

    public function test_destroy_denies_user_without_delete_permission(): void
    {
        $campus = $this->createCampusForDeletion();
        $user = User::factory()->create();

        $this->actingAs($user)
            ->delete(route('campuses.destroy', $campus))
            ->assertForbidden();

        $this->assertDatabaseHas('campuses', [
            'id' => $campus->getKey(),
        ], 'mysql');
    }

    public function test_destroy_deletes_campus_but_preserves_address(): void
    {
        $user = User::factory()->create();
        $this->grantPermission($user, 'campuses.delete');

        $campus = $this->createCampusForDeletion();
        $campusId = $campus->getKey();
        $addressId = $campus->address_id;

        $this->actingAs($user)
            ->delete(route('campuses.destroy', $campus))
            ->assertRedirect(route('campuses.index'))
            ->assertSessionHas('success', 'Sede eliminada correctamente.');

        $this->assertDatabaseMissing('campuses', [
            'id' => $campusId,
        ], 'mysql');

        $this->assertDatabaseHas('addresses', [
            'id' => $addressId,
        ], 'mysql');
    }


    public function test_index_hides_delete_action_without_delete_permission(): void
    {
        $user = User::factory()->create();
        $this->grantPermission($user, 'campuses.view');

        $campus = $this->createCampusForDeletion();

        $this->actingAs($user)
            ->get(route('campuses.index'))
            ->assertOk()
            ->assertDontSee(
                'action="' . route('campuses.destroy', $campus) . '"',
                false,
            );
    }


    private function grantPermission(User $user, string $name): void
    {
        $permission = Permission::query()->firstOrCreate([
            'name' => $name,
        ]);

        $user->permissions()->attach($permission);
    }

    private function createCampusForDeletion(): Campus
    {
        $institutionId = (string) Str::ulid();
        $institutionShortCode = 'IESB' . $this->randomLetters(8);

        $institution = Institution::query()->create([
            'id' => $institutionId,
            'code' => 'CEN' . Str::upper(Str::random(17)),
            'short_code' => $institutionShortCode,
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
                $institutionShortCode,
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
        return substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, $length);
    }
}
