<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\Institution\Domain;

use App\Modules\Institution\Domain\Contracts\InstitutionCodeGeneratorInterface;
use App\Modules\Institution\Domain\Contracts\InstitutionCreatorInterface;
use App\Modules\Institution\Domain\Contracts\InstitutionRepositoryInterface;
use App\Modules\Institution\Domain\Contracts\InstitutionIdGeneratorInterface;
use App\Modules\Institution\Domain\DTO\InstitutionCreateData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class InstitutionCreationPersistenceTest extends TestCase
{
    use RefreshDatabase;

    public function test_creator_creates_and_repository_persists_institution(): void
    {
        $this->mock(
            InstitutionIdGeneratorInterface::class,
            function ($mock): void {
                $mock->shouldReceive('generate')
                    ->once()
                    ->andReturn('01K3TEST000000000000000010');
            }
        );

        $this->mock(
            InstitutionCodeGeneratorInterface::class,
            function ($mock): void {
                $mock->shouldReceive('generate')
                    ->once()
                    ->andReturn('CEN-000010');
            }
        );

        $creator = app(InstitutionCreatorInterface::class);
        $repository = app(InstitutionRepositoryInterface::class);

        $institution = $creator->create(
            new InstitutionCreateData(
                name: 'Institución CENICOM',
            )
        );

        $saved = $repository->save($institution);

        $this->assertSame(
            '01K3TEST000000000000000010',
            $saved->id()
        );

        $this->assertSame(
            'CEN-000010',
            $saved->code()
        );

        $this->assertSame(
            'Institución CENICOM',
            $saved->name()
        );

        $this->assertNull(
            $saved->officialRegistration()
        );

        $this->assertSame(
            'draft',
            $saved->status()
        );

        $this->assertDatabaseHas('institutions', [
            'id' => '01K3TEST000000000000000010',
            'code' => 'CEN-000010',
            'name' => 'Institución CENICOM',
            'official_registration_country' => null,
            'official_registration_authority' => null,
            'official_registration_value' => null,
            'status' => 'draft',
        ]);
    }
}
