<?php

declare(strict_types=1);

namespace App\Core\Services;

use App\Models\Currency;

use App\Core\Contracts\CurrencyRepositoryInterface;

use App\Core\Contracts\CurrencyServiceInterface;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Servicio del módulo Currency.
 *
 * Centraliza la lógica de negocio y coordina las operaciones
 * sobre el repositorio.
 *
 * @package App\Core\Services
 */
final readonly class CurrencyService
    implements CurrencyServiceInterface
{
    /**
     * Constructor.
     */
    public function __construct(
        private CurrencyRepositoryInterface $repository,
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
    ): Currency {

        return $this->repository->create($data);
    }

    /**
     * {@inheritDoc}
     */
    public function update(
        Currency $currency,
        array $data
    ): bool {

        return $this->repository->update(
            $currency,
            $data
        );
    }

    /**
     * {@inheritDoc}
     */
    public function destroy(
        Currency $currency
    ): bool {

        return $this->repository->delete(
            $currency
        );
    }
}
