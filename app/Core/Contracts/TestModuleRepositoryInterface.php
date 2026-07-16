<?php

declare(strict_types=1);

namespace App\Core\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\TestModule;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Contrato del repositorio de TestModule.
 *
 * Define las operaciones de acceso a datos del módulo.
 *
 * @package App\Core\Contracts
 */
interface TestModuleRepositoryInterface
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
    ): TestModule;

    /**
     * Actualiza un registro.
     *
     * @param array<string,mixed> $data
     */
    public function update(
        TestModule $testModule,
        array $data
    ): bool;

    /**
     * Elimina un registro.
     */
    public function delete(
        TestModule $testModule
    ): bool;
}
