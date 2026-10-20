<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\Campus\Infrastructure\Persistence;

use App\Modules\Address\Domain\Contracts\AddressServiceInterface;
use App\Modules\Campus\Domain\Contracts\CampusRepositoryInterface;
use App\Modules\Campus\Domain\DTO\CampusCreateData;
use App\Modules\Campus\Domain\Entity\Campus;
use App\Modules\Campus\Domain\Services\CampusCreationUseCase;
use App\Modules\Institution\Domain\Contracts\InstitutionCreatorInterface;
use App\Modules\Institution\Domain\Contracts\InstitutionRepositoryInterface;
use Illuminate\Support\Facades\DB;
use PDOException;
use Tests\TestCase;

final class CampusCreationRetryTest extends TestCase
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

    public function test_retries_the_complete_transaction_and_does_not_consume_sequence_on_failure(): void
    {
        $institution = app(InstitutionCreatorInterface::class)->create(
            new \App\Modules\Institution\Domain\DTO\InstitutionCreateData(
                name: 'Institución para prueba de reintento',
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
            'address' => 'Dirección para prueba de reintento',
            'neighborhood' => 'Centro',
        ]);

        DB::table('campus_code_sequences')->insert([
            'institution_id' => $institution->id(),
            'current_value' => 5,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $realRepository = app(CampusRepositoryInterface::class);
        $repository = $this->createMock(CampusRepositoryInterface::class);

        $saveAttempts = 0;
        $failedCampusId = null;

        $repository
            ->expects($this->exactly(2))
            ->method('save')
            ->willReturnCallback(
                function (Campus $campus) use (
                    $realRepository,
                    &$saveAttempts,
                    &$failedCampusId,
                ): Campus {
                    $persisted = $realRepository->save($campus);
                    $saveAttempts++;

                    if ($saveAttempts === 1) {
                        $failedCampusId = $persisted->id();

                        throw new PDOException(
                            'Simulated serialization failure after persistence.',
                            40001,
                        );
                    }

                    return $persisted;
                }
            );

        app()->instance(CampusRepositoryInterface::class, $repository);

        try {
            $data = new CampusCreateData(
                institutionId: $institution->id(),
                institutionShortCode: $institution->shortCode(),
                shortCode: 'RETRYSEDE',
                name: 'Sede creada tras reintento',
                addressId: $address->getKey(),
            );

            $campus = app(CampusCreationUseCase::class)->execute($data);
        } finally {
            app()->instance(
                CampusRepositoryInterface::class,
                $realRepository,
            );
        }

        $this->assertSame(2, $saveAttempts);
        $this->assertNotNull($failedCampusId);
        $this->assertNotSame($failedCampusId, $campus->id());

        $this->assertDatabaseMissing(
            'campuses',
            ['id' => $failedCampusId],
            'mysql',
        );

        $expectedCode = sprintf(
            'CEN-%s-RETRYSEDE-006',
            $institution->shortCode(),
        );

        $this->assertSame($expectedCode, $campus->code());

        $this->assertDatabaseHas(
            'campuses',
            [
                'id' => $campus->id(),
                'institution_id' => $institution->id(),
                'code' => $expectedCode,
            ],
            'mysql',
        );

        $this->assertSame(
            1,
            DB::table('campuses')
                ->where('institution_id', $institution->id())
                ->count(),
            'Solo debe quedar persistida la sede del intento exitoso.',
        );

        $this->assertDatabaseHas(
            'campus_code_sequences',
            [
                'institution_id' => $institution->id(),
                'current_value' => 6,
            ],
            'mysql',
        );
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
