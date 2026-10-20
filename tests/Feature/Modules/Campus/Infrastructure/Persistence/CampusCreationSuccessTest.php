<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\Campus\Infrastructure\Persistence;

use App\Modules\Address\Domain\Contracts\AddressServiceInterface;
use App\Modules\Campus\Domain\DTO\CampusCreateData;
use App\Modules\Campus\Domain\Entity\Campus;
use App\Modules\Campus\Domain\Services\CampusCreationUseCase;
use App\Modules\Institution\Domain\Contracts\InstitutionCreatorInterface;
use App\Modules\Institution\Domain\Contracts\InstitutionRepositoryInterface;
use App\Modules\Institution\Domain\DTO\InstitutionCreateData;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

final class CampusCreationSuccessTest extends TestCase
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

        $this->assertSame(
            'cenicom',
            DB::connection()->getDatabaseName(),
            'La prueba debe ejecutarse contra la base de datos cenicom.',
        );
    }

    public function test_creates_and_returns_persisted_campus_with_sequence_one(): void
    {
        $institution = app(InstitutionCreatorInterface::class)->create(
            new InstitutionCreateData(
                name: 'Institución para prueba exitosa',
                shortCode: $this->uniqueInstitutionShortCode(),
            )
        );

        $institutionRepository = app(InstitutionRepositoryInterface::class);

        $institutionRepository->save($institution);

        $this->assertNotNull(
            $institutionRepository->transitionStatus(
                $institution->id(),
                'draft',
                'active',
            ),
            'La institución de prueba debe activarse antes de crear la sede.',
        );

        $address = app(AddressServiceInterface::class)->create([
            'country_id' => 1,
            'state_id' => 1,
            'city_id' => 1,
            'address' => 'Dirección para prueba exitosa',
            'neighborhood' => 'Centro',
        ]);

        $data = new CampusCreateData(
            institutionId: $institution->id(),
            institutionShortCode: $institution->shortCode(),
            shortCode: 'SEDEUNO',
            name: 'Sede creada mediante caso de uso',
            addressId: $address->getKey(),
            ministryCode: null,
        );

        $campus = app(CampusCreationUseCase::class)->execute($data);

        $this->assertInstanceOf(Campus::class, $campus);

        $this->assertMatchesRegularExpression(
            '/^CEN-' . preg_quote($institution->shortCode(), '/') . '-SEDEUNO-001$/',
            $campus->code(),
        );

        $this->assertDatabaseHas('campuses', [
            'id' => $campus->id(),
            'institution_id' => $institution->id(),
            'code' => $campus->code(),
            'short_code' => 'SEDEUNO',
            'name' => 'Sede creada mediante caso de uso',
            'address_id' => $address->getKey(),
            'status' => 'draft',
        ], 'mysql');

        $this->assertDatabaseHas('campus_code_sequences', [
            'institution_id' => $institution->id(),
            'current_value' => 1,
        ], 'mysql');
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
