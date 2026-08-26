<?php

declare(strict_types=1);

namespace Tests\Unit\Modules\Institution\Repositories;

use App\Modules\Institution\Domain\Contracts\InstitutionRepositoryInterface;
use App\Modules\Institution\Domain\Entity\Institution as DomainInstitution;
use App\Modules\Institution\Domain\ValueObjects\InstitutionOfficialRegistration;
use App\Modules\Institution\Models\Institution as InstitutionModel;
use App\Modules\Institution\Repositories\InstitutionRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class InstitutionRepositoryTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function repository_implements_contract(): void
    {
        $repository = app(InstitutionRepository::class);

        $this->assertInstanceOf(
            InstitutionRepositoryInterface::class,
            $repository
        );
    }

    #[Test]
    public function repository_uses_institution_model(): void
    {
        $repository = app(InstitutionRepository::class);

        $this->assertInstanceOf(
            InstitutionRepository::class,
            $repository
        );

        $model = InstitutionModel::query()->getModel();

        $this->assertInstanceOf(
            InstitutionModel::class,
            $model
        );
    }

    #[Test]
    public function save_persists_domain_institution(): void
    {
        $repository = app(InstitutionRepository::class);

        $institution = new DomainInstitution(
            id: '01K3TEST000000000000000001',
            name: 'Institución CENICOM',
            code: 'CEN-001',
            officialRegistration: new InstitutionOfficialRegistration(
                country: 'CO',
                authority: 'Ministerio de Educación',
                value: 'REG-001',
            ),
        );

        $saved = $repository->save($institution);

        $this->assertSame(
            $institution->id(),
            $saved->id()
        );

        $this->assertDatabaseHas('institutions', [
            'id' => '01K3TEST000000000000000001',
            'name' => 'Institución CENICOM',
            'code' => 'CEN-001',
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

        $institution = new DomainInstitution(
            id: '01K3TEST000000000000000002',
            name: 'Institución Prueba',
            code: 'CEN-002',
            officialRegistration: new InstitutionOfficialRegistration(
                country: 'CO',
                authority: 'MEN',
                value: 'REG-002',
            ),
        );

        $saved = $repository->save($institution);

        $registration = $saved->officialRegistration();

        $this->assertNotNull($registration);
        $this->assertSame('CO', $registration->country);
        $this->assertSame('MEN', $registration->authority);
        $this->assertSame('REG-002', $registration->value);
    }

    #[Test]
    public function save_persists_institution_without_official_registration(): void
    {
        $repository = app(InstitutionRepository::class);

        $institution = new DomainInstitution(
            id: '01K3TEST000000000000000003',
            name: 'Institución Sin Registro',
            code: 'CEN-003',
        );

        $saved = $repository->save($institution);

        $this->assertNull(
            $saved->officialRegistration()
        );

        $this->assertDatabaseHas('institutions', [
            'id' => '01K3TEST000000000000000003',
            'name' => 'Institución Sin Registro',
            'code' => 'CEN-003',
            'official_registration_country' => null,
            'official_registration_authority' => null,
            'official_registration_value' => null,
            'status' => 'draft',
        ]);
    }
}
