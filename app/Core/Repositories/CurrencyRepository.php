<?php

declare(strict_types=1);

namespace App\Core\Repositories;

use App\Models\Currency;

use App\Core\Contracts\CurrencyRepositoryInterface;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Repositorio de Currency.
 *
 * Encapsula el acceso a datos del modelo y centraliza
 * todas las operaciones de persistencia.
 *
 * @package App\Core\Repositories
 */
final readonly class CurrencyRepository
    implements CurrencyRepositoryInterface
{
    /**
     * Constructor.
     */
    public function __construct(
        private Currency $model,
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
    ): Currency {

        return $this->model->create($data);
    }

    /**
     * Actualiza un registro.
     *
     * @param array<string,mixed> $data
     */
    public function update(
        Currency $currency,
        array $data
    ): bool {

        return $currency->update($data);
    }

    /**
     * Elimina un registro.
     */
    public function delete(
        Currency $currency
    ): bool {

        return $currency->delete();
    }
}
