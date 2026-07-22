<?php

declare(strict_types=1);

namespace App\Core\Contracts;

use App\Models\Currency;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Contrato del servicio de Currency.
 *
 * Define las operaciones de negocio del módulo.
 *
 * @package App\Core\Contracts
 */
interface CurrencyServiceInterface
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
    ): Currency;


    /**
     * Actualiza un registro.
     *
     * @param array<string,mixed> $data
     */
    public function update(
        Currency $currency,
        array $data
    ): bool;


    /**
     * Elimina un registro.
     */
    public function destroy(
        Currency $currency
    ): bool;
}
