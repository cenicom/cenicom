<?php

declare(strict_types=1);

namespace Tests\Unit\Modules\Institution\Repositories;

use App\Modules\Institution\Domain\Contracts\InstitutionRepositoryInterface;
use App\Modules\Institution\Domain\Entity\Institution as DomainInstitution;
use App\Modules\Institution\Domain\ValueObjects\InstitutionOfficialRegistration;
use App\Modules\Institution\Models\Institution as InstitutionModel;
use App\Modules\Institution\Repositories\InstitutionRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class InstitutionRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private function makeInstitution(
        string $id = '01K3TEST000000000000000001',
        string $status = 'draft',
        string $name = 'Institución CENICOM',
        string $code = 'CEN-001',
        string $shortCode = 'IESBSC',
    ): DomainInstitution {
        return new DomainInstitution(
            id: $id,
            name: $name,
            code: $code,
            shortCode: $shortCode,
            officialRegistration: new InstitutionOfficialRegistration(
                country: 'CO',
                authority: 'Ministerio de Educación',
                value: 'REG-001',
            ),
            status: $status,
        );
    }

    #[Test]
    public function repository_implements_contract(): void
    {
        $repository = app(InstitutionRepository::class);

        $this->assertInstanceOf(
            InstitutionRepositoryInterface::class,
            $repository,
        );
    }

    #[Test]
    public function repository_uses_institution_model(): void
    {
        $repository = app(InstitutionRepository::class);

        $this->assertInstanceOf(InstitutionRepository::class, $repository);
        $this->assertInstanceOf(
            InstitutionModel::class,
            InstitutionModel::query()->getModel(),
        );
    }

    #[Test]
    public function save_persists_domain_institution(): void
    {
        $repository = app(InstitutionRepository::class);
        $institution = $this->makeInstitution();

        $saved = $repository->save($institution);

        $this->assertSame($institution->id(), $saved->id());
        $this->assertSame('IESBSC', $saved->shortCode());

        $this->assertDatabaseHas('institutions', [
            'id' => $institution->id(),
            'name' => 'Institución CENICOM',
            'code' => 'CEN-001',
            'short_code' => 'IESBSC',
            'official_registration_country' => 'CO',
            'official_registration_authority' => 'Ministerio de Educación',
            'official_registration_value' => 'REG-001',
            'status' => 'draft',
        ]);
    }

    #[Test]
    public function save_reconstructs_official_registration(): void
    {
        $repository = app(InstitutionRepository::class);

        $saved = $repository->save($this->makeInstitution(
            id: '01K3TEST000000000000000002',
            name: 'Institución Prueba',
            code: 'CEN-002',
            shortCode: 'IEPRUE',
        ));

        $this->assertSame('IEPRUE', $saved->shortCode());

        $registration = $saved->officialRegistration();

        $this->assertNotNull($registration);
        $this->assertSame('CO', $registration->country);
        $this->assertSame('Ministerio de Educación', $registration->authority);
        $this->assertSame('REG-001', $registration->value);
    }

    #[Test]
    public function save_persists_institution_without_official_registration(): void
    {
        $repository = app(InstitutionRepository::class);

        $institution = new DomainInstitution(
            id: '01K3TEST000000000000000003',
            name: 'Institución Sin Registro',
            code: 'CEN-003',
            shortCode: 'ISRTEST',
        );

        $saved = $repository->save($institution);

        $this->assertSame('ISRTEST', $saved->shortCode());
        $this->assertNull($saved->officialRegistration());

        $this->assertDatabaseHas('institutions', [
            'id' => $institution->id(),
            'name' => 'Institución Sin Registro',
            'code' => 'CEN-003',
            'short_code' => 'ISRTEST',
            'official_registration_country' => null,
            'official_registration_authority' => null,
            'official_registration_value' => null,
            'status' => 'draft',
        ]);
    }

    #[Test]
    public function save_preserves_persisted_status_when_existing_entity_has_another_status(): void
    {
        $repository = app(InstitutionRepository::class);

        $repository->save($this->makeInstitution(status: 'draft'));

        $saved = $repository->save($this->makeInstitution(
            status: 'active',
            name: 'Institución actualizada',
        ));

        $this->assertSame('draft', $saved->status());

        $this->assertDatabaseHas('institutions', [
            'id' => '01K3TEST000000000000000001',
            'name' => 'Institución actualizada',
            'status' => 'draft',
        ]);
    }

    #[Test]
    public function update_rejects_status_changes_without_modifying_persisted_status(): void
    {
        $repository = app(InstitutionRepository::class);
        $institution = $repository->save($this->makeInstitution());

        try {
            $repository->update($institution->id(), ['status' => 'active']);
            $this->fail('Se esperaba que update rechazara el cambio de estado.');
        } catch (InvalidArgumentException $exception) {
            $this->assertSame(
                'El estado solo puede modificarse mediante una transición explícita.',
                $exception->getMessage(),
            );
        }

        $this->assertDatabaseHas('institutions', [
            'id' => $institution->id(),
            'status' => 'draft',
        ]);
    }

    #[Test]
    public function transition_status_changes_state_only_when_expected_status_matches(): void
    {
        $repository = app(InstitutionRepository::class);
        $institution = $repository->save($this->makeInstitution());

        $transitioned = $repository->transitionStatus(
            $institution->id(),
            'draft',
            'active',
        );

        $this->assertNotNull($transitioned);
        $this->assertSame('active', $transitioned->status());

        $this->assertNull($repository->transitionStatus(
            $institution->id(),
            'draft',
            'active',
        ));

        $this->assertDatabaseHas('institutions', [
            'id' => $institution->id(),
            'status' => 'active',
        ]);
    }

    #[Test]
    public function lock_for_update_returns_persisted_status_inside_transaction(): void
    {
        $repository = app(InstitutionRepository::class);
        $institution = $repository->save($this->makeInstitution());

        DB::transaction(function () use ($repository, $institution): void {
            $locked = $repository->lockForUpdate($institution->id());

            $this->assertNotNull($locked);
            $this->assertSame($institution->id(), $locked->id());
            $this->assertSame('draft', $locked->status());
        });
    }

    #[Test]
public function transition_status_rejects_forbidden_transitions(): void
{
    $repository = app(InstitutionRepository::class);
    $institution = $repository->save(
        $this->makeInstitution(status: 'draft'),
    );

    try {
        $repository->transitionStatus(
            $institution->id(),
            'draft',
            'inactive',
        );

        $this->fail('Se esperaba que la transición prohibida fuera rechazada.');
    } catch (\InvalidArgumentException $exception) {
        $this->assertSame(
            'Transición de estado de institución no permitida: draft → inactive.',
            $exception->getMessage(),
        );
    }

    $this->assertDatabaseHas('institutions', [
        'id' => $institution->id(),
        'status' => 'draft',
    ]);
}
}
