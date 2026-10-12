<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\Campus\Infrastructure\Persistence;

//use App\Core\Contracts\RepositoryInterface;
use App\Modules\Address\Domain\Contracts\AddressServiceInterface;
use App\Modules\Campus\Domain\Contracts\CampusRepositoryInterface;
use App\Modules\Campus\Domain\DTO\CampusCreateData;
use App\Modules\Campus\Domain\Entity\Campus;
use App\Modules\Campus\Domain\Services\CampusCreationUseCase;
use App\Modules\Institution\Domain\Contracts\InstitutionCreatorInterface;
use App\Modules\Institution\Domain\Contracts\InstitutionRepositoryInterface;
use App\Modules\Institution\Domain\DTO\InstitutionCreateData;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Tests\TestCase;

final class CampusCreationRollbackTest extends TestCase
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

    public function test_rolls_back_campus_and_sequence_when_persistence_fails_after_save(): void
    {
        $institutionCreator = app(InstitutionCreatorInterface::class);
        $institutionRepository = app(InstitutionRepositoryInterface::class);

        $institution = $institutionCreator->create(
            new InstitutionCreateData(
                name: 'Institución para prueba de reversión',
                shortCode: $this->uniqueInstitutionShortCode(),
            )
        );

        $institutionRepository->save($institution);

        $address = app(AddressServiceInterface::class)->create([
            'country_id' => 1,
            'state_id' => 1,
            'city_id' => 1,
            'address' => 'Dirección para prueba de reversión',
            'neighborhood' => 'Centro',
        ]);

        DB::table('campus_code_sequences')->insert([
            'institution_id' => $institution->id(),
            'current_value' => 5,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $realRepository = app(CampusRepositoryInterface::class);

        $decorator = new CampusRepositorySaveThenFail($realRepository);

        app()->instance(CampusRepositoryInterface::class, $decorator);

        $data = new CampusCreateData(
            institutionId: $institution->id(),
            institutionShortCode: $institution->shortCode(),
            shortCode: 'ROLLBACK',
            name: 'Sede que debe revertirse',
            addressId: $address->getKey(),
            ministryCode: null,
        );

        $exceptionCaught = false;

        try {
            app(CampusCreationUseCase::class)->execute($data);
        } catch (CampusCreationRollbackException) {
            $exceptionCaught = true;
        }

        $this->assertTrue(
            $exceptionCaught,
            'El caso de uso debe propagar la excepción provocada después de persistir.',
        );

        $this->assertTrue(
            $decorator->persistedBeforeFailure,
            'El decorador debe confirmar que la sede existía antes de lanzar la excepción.',
        );

        $this->assertDatabaseMissing('campuses', [
            'id' => $decorator->persistedCampusId,
        ], 'mysql');

        $this->assertDatabaseHas('campus_code_sequences', [
            'institution_id' => $institution->id(),
            'current_value' => 5,
        ], 'mysql');
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

/**
 * Decorador exclusivo de la prueba: delega las operaciones reales
 * y provoca un fallo inmediatamente después de guardar la sede.
 */
final class CampusRepositorySaveThenFail implements CampusRepositoryInterface
{
    public bool $persistedBeforeFailure = false;

    public ?string $persistedCampusId = null;

    public function __construct(
        private readonly CampusRepositoryInterface $repository,
    ) {}

    public function save(Campus $campus): Campus
    {
        $persisted = $this->repository->save($campus);

        $this->persistedCampusId = $persisted->id();

        $this->persistedBeforeFailure = DB::table('campuses')
            ->where('id', $this->persistedCampusId)
            ->exists();

        if (! $this->persistedBeforeFailure) {
            throw new RuntimeException(
                'La sede no estaba presente después de delegar la persistencia.'
            );
        }

        throw new CampusCreationRollbackException(
            'Fallo controlado después de persistir la sede.'
        );
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage);
    }

    public function all(): Collection
    {
        return $this->repository->all();
    }

    public function findById(
        int|string $id,
        array $columns = ['*']
    ): ?Model {
        return $this->repository->findById($id, $columns);
    }

    public function findOrFail(
        int|string $id,
        array $columns = ['*']
    ): Model {
        return $this->repository->findOrFail($id, $columns);
    }

    public function create(array $attributes): Model
    {
        return $this->repository->create($attributes);
    }

    public function update(
        int|string $id,
        array $attributes
    ): bool {
        return $this->repository->update($id, $attributes);
    }

    public function delete(int|string $id): bool
    {
        return $this->repository->delete($id);
    }

    public function restore(int|string $id): bool
    {
        return $this->repository->restore($id);
    }

    public function forceDelete(int|string $id): bool
    {
        return $this->repository->forceDelete($id);
    }

    public function exists(int|string $id): bool
    {
        return $this->repository->exists($id);
    }

    public function count(): int
    {
        return $this->repository->count();
    }
}

final class CampusCreationRollbackException extends RuntimeException
{
}
