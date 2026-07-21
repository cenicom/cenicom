<?php

declare(strict_types=1);

namespace App\Core\Contracts;

use App\Models\TestForm;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Contrato del servicio de TestForm.
 *
 * Define las operaciones de negocio del módulo.
 *
 * @package App\Core\Contracts
 */
interface TestFormServiceInterface
{
    /**
     * Obtiene registros paginados.
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
    public function destroy(
        TestForm $testForm
    ): bool;
}
