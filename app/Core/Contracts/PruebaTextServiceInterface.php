<?php

declare(strict_types=1);

namespace App\Core\Services\Contracts;

use App\Models\PruebaText;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Contrato del servicio de PruebaText.
 *
 * Define las operaciones de negocio del módulo.
 *
 * @package App\Core\Services\Contracts
 */
interface PruebaTextServiceInterface
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
    public function destroy(
        PruebaText $pruebaText
    ): bool;
}
