<?php

declare(strict_types=1);

namespace Tests\Unit\Modules\Institution\Actions;

use App\Core\Audit\Contracts\AuditRecorderInterface;
use App\Core\Security\Contracts\IdentityInterface;
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

        $identity = Mockery::mock(IdentityInterface::class);
        $auditRecorder = Mockery::mock(AuditRecorderInterface::class);

        $identity
            ->shouldReceive('id')
            ->once()
            ->andReturn(1);

        $identity
            ->shouldReceive('name')
            ->once()
            ->andReturn('Test User');

        $identity
            ->shouldReceive('authenticated')
            ->once()
            ->andReturn(true);

        $auditRecorder
            ->shouldReceive('record')
            ->once();

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
            auditRecorder: $auditRecorder,
            identity: $identity,
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

    public function test_creates_institution_with_official_registration(): void
    {
        $auditRecorder = Mockery::mock(
            AuditRecorderInterface::class,
        );

        $identity = Mockery::mock(
            IdentityInterface::class,
        );

        $creator = Mockery::mock(InstitutionCreatorInterface::class);
        $repository = Mockery::mock(InstitutionRepositoryInterface::class);
        $service = Mockery::mock(InstitutionServiceInterface::class);

        $identity
            ->shouldReceive('id')
            ->once()
            ->andReturn(1);

        $identity
            ->shouldReceive('name')
            ->once()
            ->andReturn('Test User');

        $identity
            ->shouldReceive('authenticated')
            ->once()
            ->andReturn(true);

        $auditRecorder
            ->shouldReceive('record')
            ->once();

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
            auditRecorder: $auditRecorder,
            identity: $identity,
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
        $auditRecorder = Mockery::mock(
            AuditRecorderInterface::class,
        );

        $identity = Mockery::mock(
            IdentityInterface::class,
        );

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

        $identity
            ->shouldReceive('id')
            ->once()
            ->andReturn(1);

        $identity
            ->shouldReceive('name')
            ->once()
            ->andReturn('Test User');

        $identity
            ->shouldReceive('authenticated')
            ->once()
            ->andReturn(true);

        $auditRecorder
            ->shouldReceive('record')
            ->once();

        $repository
            ->shouldReceive('save')
            ->once()
            ->with($createdInstitution)
            ->andReturn($savedInstitution);

        $action = new InstitutionAction(
            creator: $creator,
            repository: $repository,
            service: $service,
            auditRecorder: $auditRecorder,
            identity: $identity,
        );

        $result = $action->create([
            'name' => 'Institución Creada',
        ]);

        $this->assertSame($savedInstitution, $result);
    }

    public function test_updates_institution_through_service(): void
    {
        $auditRecorder = Mockery::mock(
            AuditRecorderInterface::class,
        );

        $identity = Mockery::mock(
            IdentityInterface::class,
        );

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
            auditRecorder: $auditRecorder,
            identity: $identity,
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
        $auditRecorder = Mockery::mock(
            AuditRecorderInterface::class,
        );

        $identity = Mockery::mock(
            IdentityInterface::class,
        );

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
            auditRecorder: $auditRecorder,
            identity: $identity,
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
        $auditRecorder = Mockery::mock(AuditRecorderInterface::class);
        $identity = Mockery::mock(IdentityInterface::class);

        $service
            ->shouldReceive('delete')
            ->once()
            ->with('01JTESTINSTITUTION000000000005')
            ->andReturnTrue();

        $action = new InstitutionAction(
            creator: $creator,
            repository: $repository,
            service: $service,
            auditRecorder: $auditRecorder,
            identity: $identity,
        );

        $result = $action->delete(
            '01JTESTINSTITUTION000000000005'
        );

        $this->assertTrue($result);
    }
}
