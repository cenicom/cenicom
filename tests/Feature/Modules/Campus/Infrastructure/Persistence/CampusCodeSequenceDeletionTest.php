<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\Campus\Infrastructure\Persistence;

use App\Modules\Address\Domain\Contracts\AddressServiceInterface;
use App\Modules\Campus\Domain\DTO\CampusCreateData;
use App\Modules\Campus\Domain\Services\CampusCreationUseCase;
use App\Modules\Campus\Domain\Contracts\CampusRepositoryInterface;
use App\Modules\Institution\Domain\Contracts\InstitutionCreatorInterface;
use App\Modules\Institution\Domain\Contracts\InstitutionRepositoryInterface;
use App\Modules\Institution\Domain\DTO\InstitutionCreateData;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

final class CampusCodeSequenceDeletionTest extends TestCase
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
            'La prueba debe ejecutarse exclusivamente sobre la base cenicom.',
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

    public function test_deleting_a_campus_does_not_reset_or_reuse_its_sequence(): void
    {
        $institution = app(InstitutionCreatorInterface::class)->create(
            new InstitutionCreateData(
                name: 'Institución para prueba de secuencia',
                shortCode: $this->uniqueInstitutionShortCode(),
            )
        );

        app(InstitutionRepositoryInterface::class)->save($institution);

        $address = app(AddressServiceInterface::class)->create([
            'country_id' => 1,
            'state_id' => 1,
            'city_id' => 1,
            'address' => 'Dirección para prueba de secuencia',
            'neighborhood' => 'Centro',
        ]);

        $institutionId = $institution->id();
        $institutionShortCode = $institution->shortCode();
        $addressId = $address->getKey();

        $useCase = app(CampusCreationUseCase::class);
        $repository = app(CampusRepositoryInterface::class);

        $firstCampus = $useCase->execute(
            new CampusCreateData(
                institutionId: $institutionId,
                institutionShortCode: $institutionShortCode,
                shortCode: 'SEDEUNO',
                name: 'Sede para eliminación',
                addressId: $addressId,
                ministryCode: null,
            )
        );

        $firstCampusId = $firstCampus->id();

        $this->assertSame(
            sprintf('CEN-%s-SEDEUNO-001', $institutionShortCode),
            $firstCampus->code(),
        );

        $this->assertDatabaseHas('campus_code_sequences', [
            'institution_id' => $institutionId,
            'current_value' => 1,
        ], 'mysql');

        $this->assertTrue(
            $repository->delete($firstCampusId),
            'La primera sede debe eliminarse correctamente.',
        );

        $this->assertDatabaseMissing('campuses', [
            'id' => $firstCampusId,
        ], 'mysql');

        $this->assertDatabaseHas('campus_code_sequences', [
            'institution_id' => $institutionId,
            'current_value' => 1,
        ], 'mysql');

        $secondCampus = $useCase->execute(
            new CampusCreateData(
                institutionId: $institutionId,
                institutionShortCode: $institutionShortCode,
                shortCode: 'SEDEDOS',
                name: 'Sede posterior a eliminación',
                addressId: $addressId,
                ministryCode: null,
            )
        );

        $this->assertSame(
            sprintf('CEN-%s-SEDEDOS-002', $institutionShortCode),
            $secondCampus->code(),
        );

        $this->assertDatabaseHas('campuses', [
            'id' => $secondCampus->id(),
            'code' => sprintf(
                'CEN-%s-SEDEDOS-002',
                $institutionShortCode,
            ),
        ], 'mysql');

        $this->assertDatabaseHas('campus_code_sequences', [
            'institution_id' => $institutionId,
            'current_value' => 2,
        ], 'mysql');
    }


    private function uniqueInstitutionShortCode(): string
    {
        do {
            $shortCode = 'IESBSP' . strtoupper(
                substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 6),
            );
        } while (
            DB::connection('mysql')
            ->table('institutions')
            ->where('short_code', $shortCode)
            ->exists()
        );

        return $shortCode;
    }
}
