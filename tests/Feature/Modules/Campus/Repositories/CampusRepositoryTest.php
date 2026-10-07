<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\Campus\Repositories;

use App\Modules\Campus\Domain\Entity\Campus as DomainCampus;
use App\Modules\Campus\Repositories\CampusRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class CampusRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_repository_saves_and_reconstructs_campus(): void
    {
        $campus = new DomainCampus(
            id: '01K3CAMPUS000000000000001',
            institutionId: '01K3INSTITUTION000000000001',
            code: 'CEN-IESB-SBB-001',
            shortCode: 'SBB',
            name: 'Sede Bachillerato',
            addressId: '01K3ADDRESS000000000000001',
            ministryCode: 'MIN-001',
            status: 'draft',
        );

        $repository = new CampusRepository();

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

        $this->assertDatabaseHas('campuses', [
            'id' => '01K3CAMPUS000000000000001',
            'institution_id' => '01K3INSTITUTION000000000001',
            'code' => 'CEN-IESB-SBB-001',
            'short_code' => 'SBB',
            'name' => 'Sede Bachillerato',
            'ministry_code' => 'MIN-001',
            'address_id' => '01K3ADDRESS000000000000001',
            'status' => 'draft',
        ]);
    }
}
