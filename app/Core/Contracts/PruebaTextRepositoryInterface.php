<?php

declare(strict_types=1);

namespace App\Core\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

use App\Models\PruebaText;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Contrato del repositorio de PruebaText.
 *
 * Define las operaciones de acceso a datos del módulo.
 *
 * @package App\Core\Repositories\Contracts
 */
interface PruebaTextRepositoryInterface
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
    ): PruebaText;

    /**
     * Actualiza un registro.
     *
     * @param array<string,mixed> $data
     */
    public function update(
        PruebaText $pruebaText,
        array $data
    ): bool;

    /**
     * Elimina un registro.
     */
    public function delete(
        PruebaText $pruebaText
    ): bool;
}
