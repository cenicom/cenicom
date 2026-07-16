<?php

declare(strict_types=1);

namespace App\Core\Repositories;

use App\Models\TestModule;
use App\Core\Contracts\TestModuleRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Repositorio de TestModule.
 *
 * Encapsula el acceso a datos del modelo y centraliza
 * todas las operaciones de persistencia.
 *
 * @package App\Core\Repositories
 */
final readonly class TestModuleRepository
    implements TestModuleRepositoryInterface
{
    /**
     * Constructor.
     */
    public function __construct(
        private TestModule $model,
    ) {
    }

    /**
     * Obtiene el listado paginado.
     */
    public function paginate(
        int $perPage = 15
    ): LengthAwarePaginator {

        return $this->model
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Crea un nuevo registro.
     *
     * @param array<string,mixed> $data
     */
    public function create(
        array $data
    ): TestModule {

        return $this->model->create($data);
    }

    /**
     * Actualiza un registro.
     *
     * @param array<string,mixed> $data
     */
    public function update(
        TestModule $testModule,
        array $data
    ): bool {

        return $testModule->update($data);
    }

    /**
     * Elimina un registro.
     */
    public function delete(
        TestModule $testModule
    ): bool {

        return $testModule->delete();
    }
}
