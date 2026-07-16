<?php

declare(strict_types=1);

namespace App\Core\Services;

use App\Models\TestModule;
use App\Core\Contracts\TestModuleRepositoryInterface;
use App\Core\Contracts\TestModuleServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Servicio del módulo TestModule.
 *
 * Centraliza la lógica de negocio y coordina las operaciones
 * sobre el repositorio.
 *
 * @package App\Core\Services
 */
final readonly class TestModuleService
    implements TestModuleServiceInterface
{
    /**
     * Constructor.
     */
    public function __construct(
        private TestModuleRepositoryInterface $repository,
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function paginate(
        int $perPage = 15
    ): LengthAwarePaginator {

        return $this->repository->paginate($perPage);
    }

    /**
     * {@inheritDoc}
     */
    public function create(
        array $data
    ): TestModule {

        return $this->repository->create($data);
    }

    /**
     * {@inheritDoc}
     */
    public function update(
        TestModule $testModule,
        array $data
    ): bool {

        return $this->repository->update(
            $testModule,
            $data
        );
    }

    /**
     * {@inheritDoc}
     */
    public function destroy(
        TestModule $testModule
    ): bool {

        return $this->repository->delete(
            $testModule
        );
    }
}
