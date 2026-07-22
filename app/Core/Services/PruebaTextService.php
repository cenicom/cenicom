<?php

declare(strict_types=1);

namespace App\Core\Services;

use App\Models\PruebaText;

use App\Core\Repositories\Contracts\PruebaTextRepositoryInterface;

use App\Core\Services\Contracts\PruebaTextServiceInterface;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Servicio del módulo PruebaText.
 *
 * Centraliza la lógica de negocio y coordina las operaciones
 * sobre el repositorio.
 *
 * @package App\Core\Services
 */
final readonly class PruebaTextService
    implements PruebaTextServiceInterface
{
    /**
     * Constructor.
     */
    public function __construct(
        private PruebaTextRepositoryInterface $repository,
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
    ): PruebaText {

        return $this->repository->create($data);
    }

    /**
     * {@inheritDoc}
     */
    public function update(
        PruebaText $pruebaText,
        array $data
    ): bool {

        return $this->repository->update(
            $pruebaText,
            $data
        );
    }

    /**
     * {@inheritDoc}
     */
    public function destroy(
        PruebaText $pruebaText
    ): bool {

        return $this->repository->delete(
            $pruebaText
        );
    }
}
