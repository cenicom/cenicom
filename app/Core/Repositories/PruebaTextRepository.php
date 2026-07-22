<?php

declare(strict_types=1);

namespace App\Core\Repositories;

use App\Models\PruebaText;

use App\Core\Repositories\Contracts\PruebaTextRepositoryInterface;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Repositorio de PruebaText.
 *
 * Encapsula el acceso a datos del modelo y centraliza
 * todas las operaciones de persistencia.
 *
 * @package App\Core\Repositories
 */
final readonly class PruebaTextRepository
    implements PruebaTextRepositoryInterface
{
    /**
     * Constructor.
     */
    public function __construct(
        private PruebaText $model,
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
    ): PruebaText {

        return $this->model->create($data);
    }

    /**
     * Actualiza un registro.
     *
     * @param array<string,mixed> $data
     */
    public function update(
        PruebaText $pruebaText,
        array $data
    ): bool {

        return $pruebaText->update($data);
    }

    /**
     * Elimina un registro.
     */
    public function delete(
        PruebaText $pruebaText
    ): bool {

        return $pruebaText->delete();
    }
}
