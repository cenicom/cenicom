<?php

declare(strict_types=1);

namespace Tests\Unit\Modules\Institution\Actions;

use App\Modules\Institution\Actions\InstitutionAction;
use App\Modules\Institution\Domain\Contracts\InstitutionCreatorInterface;
use App\Modules\Institution\Domain\Contracts\InstitutionRepositoryInterface;
use App\Modules\Institution\Domain\Contracts\InstitutionServiceInterface;
use App\Modules\Institution\Domain\DTO\InstitutionCreateData;
use App\Modules\Institution\Domain\Entity\Institution;
use App\Modules\Institution\Domain\ValueObjects\InstitutionOfficialRegistration;
use Mockery;
use Tests\TestCase;

final class InstitutionActionTest extends TestCase
{
    public function test_creates_institution_from_array_data(): void
    {
        $creator = Mockery::mock(InstitutionCreatorInterface::class);
        $repository = Mockery::mock(InstitutionRepositoryInterface::class);
        $service = Mockery::mock(InstitutionServiceInterface::class);

        $institution = new Institution(
            id: '01JTESTINSTITUTION000000000001',
            name: 'Institución Educativa Nacional Simón Bolívar',
            code: 'CEN-000001',
        );

        $creator
            ->shouldReceive('create')
            ->once()
            ->withArgs(function (InstitutionCreateData $data): bool {
                return $data->name === 'Institución Educativa Nacional Simón Bolívar'
                    && $data->officialRegistration === null;
            })
            ->andReturn($institution);

        $repository
            ->shouldReceive('save')
            ->once()
            ->with($institution)
            ->andReturn($institution);

        $action = new InstitutionAction(
            creator: $creator,
            repository: $repository,
            service: $service,
        );

        $result = $action->create([
            'name' => 'Institución Educativa Nacional Simón Bolívar',
        ]);

        $this->assertSame($institution, $result);
    }

    public function test_creates_institution_with_official_registration(): void
    {
        $creator = Mockery::mock(InstitutionCreatorInterface::class);
        $repository = Mockery::mock(InstitutionRepositoryInterface::class);
        $service = Mockery::mock(InstitutionServiceInterface::class);

        $institution = new Institution(
            id: '01JTESTINSTITUTION000000000002',
            name: 'Escuela Batalla de Boyacá',
            code: 'CEN-000002',
        );

        $creator
            ->shouldReceive('create')
            ->once()
            ->withArgs(function (InstitutionCreateData $data): bool {
                return $data->name === 'Escuela Batalla de Boyacá'
                    && $data->officialRegistration instanceof InstitutionOfficialRegistration
                    && $data->officialRegistration->country === 'CO'
                    && $data->officialRegistration->authority === 'Education Authority'
                    && $data->officialRegistration->value === '123456789';
            })
            ->andReturn($institution);

        $repository
            ->shouldReceive('save')
            ->once()
            ->with($institution)
            ->andReturn($institution);

        $action = new InstitutionAction(
            creator: $creator,
            repository: $repository,
            service: $service,
        );

        $result = $action->create([
            'name' => 'Escuela Batalla de Boyacá',
            'officialRegistration' => [
                'country' => 'CO',
                'authority' => 'Education Authority',
                'value' => '123456789',
            ],
        ]);

        $this->assertSame($institution, $result);
    }

    public function test_create_returns_institution_saved_by_repository(): void
    {
        $creator = Mockery::mock(InstitutionCreatorInterface::class);
        $repository = Mockery::mock(InstitutionRepositoryInterface::class);
        $service = Mockery::mock(InstitutionServiceInterface::class);

        $createdInstitution = new Institution(
            id: '01JTESTINSTITUTION000000000003',
            name: 'Institución Creada',
            code: 'CEN-000003',
        );

        $savedInstitution = new Institution(
            id: '01JTESTINSTITUTION000000000003',
            name: 'Institución Creada',
            code: 'CEN-000003',
        );

        $creator
            ->shouldReceive('create')
            ->once()
            ->andReturn($createdInstitution);

        $repository
            ->shouldReceive('save')
            ->once()
            ->with($createdInstitution)
            ->andReturn($savedInstitution);

        $action = new InstitutionAction(
            creator: $creator,
            repository: $repository,
            service: $service,
        );

        $result = $action->create([
            'name' => 'Institución Creada',
        ]);

        $this->assertSame($savedInstitution, $result);
    }

    public function test_updates_institution_through_service(): void
    {
        $creator = Mockery::mock(InstitutionCreatorInterface::class);
        $repository = Mockery::mock(InstitutionRepositoryInterface::class);
        $service = Mockery::mock(InstitutionServiceInterface::class);

        $service
            ->shouldReceive('update')
            ->once()
            ->with(
                '01JTESTINSTITUTION000000000004',
                [
                    'name' => 'Institución Actualizada',
                ],
            )
            ->andReturnTrue();

        $action = new InstitutionAction(
            creator: $creator,
            repository: $repository,
            service: $service,
        );

        $result = $action->update(
            '01JTESTINSTITUTION000000000004',
            [
                'name' => 'Institución Actualizada',
            ],
        );

        $this->assertTrue($result);
    }

    public function test_updates_institution_with_official_registration(): void
    {
        $creator = Mockery::mock(InstitutionCreatorInterface::class);
        $repository = Mockery::mock(InstitutionRepositoryInterface::class);
        $service = Mockery::mock(InstitutionServiceInterface::class);

        $service
            ->shouldReceive('update')
            ->once()
            ->with(
                '01JTESTINSTITUTION000000000006',
                [
                    'name' => 'Institución Actualizada',
                    'official_registration_country' => 'CO',
                    'official_registration_authority' => 'Education Authority',
                    'official_registration_value' => 'REG-002',
                ],
            )
            ->andReturnTrue();

        $action = new InstitutionAction(
            creator: $creator,
            repository: $repository,
            service: $service,
        );

        $result = $action->update(
            '01JTESTINSTITUTION000000000006',
            [
                'name' => 'Institución Actualizada',
                'officialRegistration' => [
                    'country' => 'CO',
                    'authority' => 'Education Authority',
                    'value' => 'REG-002',
                ],
            ],
        );

        $this->assertTrue($result);
    }

    public function test_deletes_institution_through_service(): void
    {
        $creator = Mockery::mock(InstitutionCreatorInterface::class);
        $repository = Mockery::mock(InstitutionRepositoryInterface::class);
        $service = Mockery::mock(InstitutionServiceInterface::class);

        $service
            ->shouldReceive('delete')
            ->once()
            ->with('01JTESTINSTITUTION000000000005')
            ->andReturnTrue();

        $action = new InstitutionAction(
            creator: $creator,
            repository: $repository,
            service: $service,
        );

        $result = $action->delete(
            '01JTESTINSTITUTION000000000005'
        );

        $this->assertTrue($result);
    }
}
