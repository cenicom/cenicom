<?php

declare(strict_types=1);

namespace App\Core\Repositories;

use App\Models\TestForm;

use App\Core\Contracts\TestFormRepositoryInterface;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Repositorio de TestForm.
 *
 * Encapsula el acceso a datos del modelo y centraliza
 * todas las operaciones de persistencia.
 *
 * @package App\Core\Repositories
 */
final readonly class TestFormRepository
    implements TestFormRepositoryInterface
{
    /**
     * Constructor.
     */
    public function __construct(
        private TestForm $model,
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
    ): TestForm {

        return $this->model->create($data);
    }

    /**
     * Actualiza un registro.
     *
     * @param array<string,mixed> $data
     */
    public function update(
        TestForm $testForm,
        array $data
    ): bool {

        return $testForm->update($data);
    }

    /**
     * Elimina un registro.
     */
    public function delete(
        TestForm $testForm
    ): bool {

        return $testForm->delete();
    }
}
