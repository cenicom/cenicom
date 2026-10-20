<?php

declare(strict_types=1);

namespace Tests\Unit\Modules\Campus\Repositories;

use App\Core\Contracts\RepositoryInterface;
use App\Core\Repositories\BaseRepository;
use App\Modules\Campus\Domain\Contracts\CampusRepositoryInterface;
use App\Modules\Campus\Domain\Entity\Campus as DomainCampus;
use App\Modules\Campus\Models\Campus;
use App\Modules\Campus\Repositories\CampusRepository;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class CampusRepositoryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->assertSame(
            'sqlite',
            config('database.default'),
            'Estas pruebas deben utilizar la conexión SQLite aislada.',
        );

        Schema::dropIfExists('campuses');
        Schema::dropIfExists('addresses');
        Schema::dropIfExists('institutions');

        Schema::create('institutions', function (Blueprint $table): void {
            $table->char('id', 26)->primary();
            $table->string('code', 20)->unique();
            $table->string('short_code', 15)->unique();
            $table->string('name', 255);
            $table->string('official_registration_country', 2)->nullable();
            $table->string('official_registration_authority', 150)->nullable();
            $table->string('official_registration_value', 100)->nullable();
            $table->string('status', 30)->default('draft');
            $table->timestamps();
        });

        Schema::create('addresses', function (Blueprint $table): void {
            $table->char('id', 26)->primary();
            $table->timestamps();
        });

        Schema::create('campuses', function (Blueprint $table): void {
            $table->char('id', 26)->primary();
            $table->char('institution_id', 26);
            $table->string('code', 40)->unique();
            $table->string('short_code', 15);
            $table->string('name', 255);
            $table->string('ministry_code', 50)->nullable();
            $table->char('address_id', 26);
            $table->string('status', 30)->default('draft');
            $table->timestamps();

            $table->unique(['institution_id', 'short_code']);

            $table->foreign('institution_id')
                ->references('id')
                ->on('institutions');

            $table->foreign('address_id')
                ->references('id')
                ->on('addresses');
        });
    }

    private function repository(): CampusRepository
    {
        return app(CampusRepository::class);
    }

    private function makeCampus(
        string $status = 'draft',
        string $id = '01K3TEST000000000000000001',
    ): DomainCampus {
        return new DomainCampus(
            id: $id,
            institutionId: '01K3TEST000000000000000101',
            code: 'CEN-IESBSC-SEDEUNO-001',
            shortCode: 'SEDEUNO',
            name: 'Sede Principal',
            addressId: '01K3TEST000000000000000201',
            ministryCode: null,
            status: $status,
        );
    }

    private function persistDependencies(): void
    {
        $now = now();

        DB::table('institutions')->insert([
            'id' => '01K3TEST000000000000000101',
            'code' => 'CEN-TEST01',
            'short_code' => 'IESBSC',
            'name' => 'Institución de prueba',
            'status' => 'active',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('addresses')->insert([
            'id' => '01K3TEST000000000000000201',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    private function persistCampus(
        string $status = 'draft',
    ): DomainCampus {
        $this->persistDependencies();

        return $this->repository()->save($this->makeCampus($status));
    }

    #[Test]
    public function campus_repository_implements_campus_repository_contract(): void
    {
        $this->assertInstanceOf(
            CampusRepositoryInterface::class,
            $this->repository(),
        );
    }

    #[Test]
    public function campus_repository_extends_base_repository(): void
    {
        $repository = $this->repository();

        $this->assertInstanceOf(BaseRepository::class, $repository);
        $this->assertInstanceOf(RepositoryInterface::class, $repository);
    }

    #[Test]
    public function save_preserves_persisted_status_when_existing_entity_has_another_status(): void
    {
        $repository = $this->repository();

        $saved = $this->persistCampus('draft');

        $updated = $repository->save(
            $this->makeCampus(
                status: 'active',
                id: $saved->id(),
            ),
        );

        $this->assertSame('draft', $updated->status());

        $this->assertDatabaseHas('campuses', [
            'id' => $saved->id(),
            'status' => 'draft',
            'name' => 'Sede Principal',
        ]);
    }

    #[Test]
    public function update_rejects_status_changes_without_modifying_persisted_status(): void
    {
        $repository = $this->repository();
        $campus = $this->persistCampus();

        try {
            $repository->update($campus->id(), ['status' => 'active']);
            $this->fail('Se esperaba que update rechazara el cambio de estado.');
        } catch (InvalidArgumentException $exception) {
            $this->assertSame(
                'El estado solo puede modificarse mediante una transición explícita.',
                $exception->getMessage(),
            );
        }

        $this->assertDatabaseHas('campuses', [
            'id' => $campus->id(),
            'status' => 'draft',
        ]);
    }

    #[Test]
    public function transition_status_changes_state_only_when_expected_status_matches(): void
    {
        $repository = $this->repository();
        $campus = $this->persistCampus();

        $transitioned = $repository->transitionStatus(
            $campus->id(),
            'draft',
            'active',
        );

        $this->assertNotNull($transitioned);
        $this->assertSame('active', $transitioned->status());

        $this->assertNull($repository->transitionStatus(
            $campus->id(),
            'inactive',
            'active',
        ));

        $this->assertDatabaseHas('campuses', [
            'id' => $campus->id(),
            'status' => 'active',
        ]);
    }

    #[Test]
    public function transition_status_rejects_invalid_target_without_modifying_persisted_status(): void
    {
        $repository = $this->repository();
        $campus = $this->persistCampus();

        try {
            $repository->transitionStatus(
                $campus->id(),
                'draft',
                'suspended',
            );
            $this->fail('Se esperaba InvalidArgumentException.');
        } catch (InvalidArgumentException $exception) {
            $this->assertSame(
                'Transición de estado de sede no permitida: draft → suspended.',
                $exception->getMessage(),
            );
        }

        $this->assertDatabaseHas('campuses', [
            'id' => $campus->id(),
            'status' => 'draft',
        ]);
    }

    #[Test]
    public function transition_status_rejects_forbidden_transition_without_modifying_persisted_status(): void
    {
        $repository = $this->repository();
        $campus = $this->persistCampus();

        try {
            $repository->transitionStatus(
                $campus->id(),
                'draft',
                'inactive',
            );
            $this->fail('Se esperaba InvalidArgumentException.');
        } catch (InvalidArgumentException $exception) {
            $this->assertSame(
                'Transición de estado de sede no permitida: draft → inactive.',
                $exception->getMessage(),
            );
        }

        $this->assertDatabaseHas('campuses', [
            'id' => $campus->id(),
            'status' => 'draft',
        ]);
    }

    #[Test]
    public function lock_for_update_returns_persisted_status_inside_transaction(): void
    {
        $repository = $this->repository();
        $campus = $this->persistCampus();

        DB::transaction(function () use ($repository, $campus): void {
            $locked = $repository->lockForUpdate($campus->id());

            $this->assertNotNull($locked);
            $this->assertSame($campus->id(), $locked->id());
            $this->assertSame('draft', $locked->status());
        });
    }


    #[Test]
    public function restore_is_not_exposed_when_campus_does_not_use_soft_deletes(): void
    {
        $this->assertFalse(
            method_exists($this->repository(), 'restore')
        );
    }

    #[Test]
    public function force_delete_is_not_exposed_when_campus_does_not_use_soft_deletes(): void
    {
        $this->assertFalse(
            method_exists($this->repository(), 'forceDelete')
        );
    }
}
