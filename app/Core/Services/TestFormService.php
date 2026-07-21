<?php

declare(strict_types=1);

namespace App\Core\Services;

use App\Models\TestForm;

use App\Core\Contracts\TestFormRepositoryInterface;

use App\Core\Contracts\TestFormServiceInterface;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Servicio del módulo TestForm.
 *
 * Centraliza la lógica de negocio y coordina las operaciones
 * sobre el repositorio.
 *
 * @package App\Core\Services
 */
final readonly class TestFormService
    implements TestFormServiceInterface
{
    /**
     * Constructor.
     */
    public function __construct(
        private TestFormRepositoryInterface $repository,
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
    ): TestForm {

        return $this->repository->create($data);
    }

    /**
     * {@inheritDoc}
     */
    public function update(
        TestForm $testForm,
        array $data
    ): bool {

        return $this->repository->update(
            $testForm,
            $data
        );
    }

    /**
     * {@inheritDoc}
     */
    public function destroy(
        TestForm $testForm
    ): bool {

        return $this->repository->delete(
            $testForm
        );
    }
}
