<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\Campus\Infrastructure\Persistence;

use App\Modules\Address\Domain\Contracts\AddressServiceInterface;
use App\Modules\Campus\Domain\Contracts\CampusCreatorInterface;
use App\Modules\Campus\Domain\Contracts\CampusRepositoryInterface;
use App\Modules\Campus\Domain\DTO\CampusCreateData;
use App\Modules\Institution\Domain\Contracts\InstitutionCreatorInterface;
use App\Modules\Institution\Domain\Contracts\InstitutionRepositoryInterface;
use App\Modules\Institution\Domain\DTO\InstitutionCreateData;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

final class CampusCreationPersistenceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'mysql',
            'database.connections.mysql.database' => 'cenicom',
        ]);

        DB::purge('mysql');
        DB::setDefaultConnection('mysql');
    }

    public function test_creates_campus_with_contractual_code_and_persists_it(): void
    {
        $institutionCreator = app(InstitutionCreatorInterface::class);
        $institutionRepository = app(InstitutionRepositoryInterface::class);

        $institution = $institutionCreator->create(
            new InstitutionCreateData(
                name: 'Institución CENICOM',
                shortCode: $this->uniqueInstitutionShortCode(),
            )
        );

        $institutionRepository->save($institution);

        $address = app(AddressServiceInterface::class)->create([
            'country_id' => 1,
            'state_id' => 1,
            'city_id' => 1,
            'address' => 'Carrera 10 # 20-30',
            'neighborhood' => 'Centro',
        ]);

        $campusCreator = app(CampusCreatorInterface::class);

        $campus = $campusCreator->create(
            new CampusCreateData(
                institutionId: $institution->id(),
                institutionShortCode: $institution->shortCode(),
                shortCode: 'SEDEUNO',
                name: 'Sede Principal',
                addressId: $address->getKey(),
                ministryCode: null,
            )
        );

        app(CampusRepositoryInterface::class)->save($campus);

        $this->assertMatchesRegularExpression(
            '/^CEN-' . preg_quote($institution->shortCode(), '/') . '-SEDEUNO-001$/',
            $campus->code()
        );

        $this->assertDatabaseHas('campuses', [
            'id' => $campus->id(),
            'institution_id' => $institution->id(),
            'code' => $campus->code(),
            'short_code' => 'SEDEUNO',
            'name' => 'Sede Principal',
            'address_id' => $address->getKey(),
            'status' => 'draft',
        ]);

        $this->assertDatabaseHas('campus_code_sequences', [
            'institution_id' => $institution->id(),
            'current_value' => 1,
        ]);
    }

    private function uniqueInstitutionShortCode(): string
    {
        do {
            $shortCode = '';

            for ($i = 0; $i < 8; $i++) {
                $shortCode .= chr(random_int(65, 90));
            }
        } while (
            DB::table('institutions')
                ->where('short_code', $shortCode)
                ->exists()
        );

        return $shortCode;
    }
}
