<?php

declare(strict_types=1);

namespace App\Core\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\TestForm;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Contrato del repositorio de TestForm.
 *
 * Define las operaciones de acceso a datos del módulo.
 *
 * @package App\Core\Contracts
 */
interface TestFormRepositoryInterface
{
    /**
     * Obtiene el listado paginado.
     */
    public function paginate(
        int $perPage = 15
    ): LengthAwarePaginator;

    /**
     * Crea un nuevo registro.
     *
     * @param array<string,mixed> $data
     */
    public function create(
        array $data
    ): TestForm;

    /**
     * Actualiza un registro.
     *
     * @param array<string,mixed> $data
     */
    public function update(
        TestForm $testForm,
        array $data
    ): bool;

    /**
     * Elimina un registro.
     */
    public function delete(
        TestForm $testForm
    ): bool;
}
