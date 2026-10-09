<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\Campus\Repositories;

use App\Modules\Address\Domain\Contracts\AddressServiceInterface;
use App\Modules\Campus\Domain\Entity\Campus as DomainCampus;
use App\Modules\Campus\Repositories\CampusRepository;
use App\Modules\Institution\Domain\Contracts\InstitutionCodeGeneratorInterface;
use App\Modules\Institution\Domain\Contracts\InstitutionCreatorInterface;
use App\Modules\Institution\Domain\Contracts\InstitutionIdGeneratorInterface;
use App\Modules\Institution\Domain\Contracts\InstitutionRepositoryInterface;
use App\Modules\Institution\Domain\DTO\InstitutionCreateData;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

final class CampusRepositoryTest extends TestCase
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

    public function test_repository_saves_and_reconstructs_campus(): void
    {
        $institutionId = '01K3TEST000000000000000010';
        $institutionCode = 'CEN-000010';

        $this->mock(
            InstitutionIdGeneratorInterface::class,
            function ($mock) use ($institutionId): void {
                $mock->shouldReceive('generate')
                    ->once()
                    ->andReturn($institutionId);
            }
        );

        $this->mock(
            InstitutionCodeGeneratorInterface::class,
            function ($mock) use ($institutionCode): void {
                $mock->shouldReceive('generate')
                    ->once()
                    ->andReturn($institutionCode);
            }
        );

        $creator = app(InstitutionCreatorInterface::class);
        $institutionRepository = app(InstitutionRepositoryInterface::class);

        $institution = $creator->create(
            new InstitutionCreateData(
                name: 'Institución CENICOM',
                shortCode: 'IECENTRAL',
            )
        );

        $savedInstitution = $institutionRepository->save($institution);

        $addressService = app(AddressServiceInterface::class);

        $address = $addressService->create([
            'country_id' => 1,
            'state_id' => 1,
            'city_id' => 1,
            'address' => 'Carrera 10 # 20-30',
            'neighborhood' => 'Centro',
        ]);

        $campus = new DomainCampus(
            id: '01K3CAMPUS000000000000001',
            institutionId: $savedInstitution->id(),
            code: 'CEN-IESBSP-SEBBPR-001',
            shortCode: 'SEBBPR',
            name: 'Sede Bachillerato',
            addressId: $address->getKey(),
            ministryCode: 'MIN-001',
            status: 'draft',
        );

        $repository = app(CampusRepository::class);

        $saved = $repository->save($campus);

        $this->assertSame(
            $campus->id(),
            $saved->id()
        );

        $this->assertSame(
            $campus->institutionId(),
            $saved->institutionId()
        );

        $this->assertSame(
            $campus->code(),
            $saved->code()
        );

        $this->assertSame(
            $campus->shortCode(),
            $saved->shortCode()
        );

        $this->assertSame(
            $campus->name(),
            $saved->name()
        );

        $this->assertSame(
            $campus->addressId(),
            $saved->addressId()
        );

        $this->assertSame(
            $campus->ministryCode(),
            $saved->ministryCode()
        );

        $this->assertSame(
            $campus->status(),
            $saved->status()
        );

        $this->assertDatabaseHas(
            'campuses',
            [
                'id' => $campus->id(),
                'institution_id' => $savedInstitution->id(),
                'code' => $campus->code(),
                'short_code' => $campus->shortCode(),
                'name' => $campus->name(),
                'ministry_code' => $campus->ministryCode(),
                'address_id' => $address->getKey(),
                'status' => $campus->status(),
            ],
            'mysql',
        );
    }
}
