<?php

declare(strict_types=1);

namespace App\Core\Services;

use App\Core\Contracts\RepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Servicio base para toda la lógica reutilizable
 * del sistema.
 *
 * @package App\Core\Services
 * @since 1.0.0
 */
abstract class BaseService
{
    /**
     * Constructor.
     */
    public function __construct(
        protected RepositoryInterface $repository
    ) {
    }

    /**
     * Obtiene un listado paginado.
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage);
    }

    /**
     * Busca un registro por ID.
     */
    public function findById(int|string $id): ?Model
    {
        return $this->repository->findById($id);
    }

    /**
     * Crea un registro.
     */
    public function create(array $attributes): Model
    {
        return $this->repository->create($attributes);
    }

    /**
     * Actualiza un registro.
     */
    public function update(Model $model, array $attributes): Model
    {
        return $this->repository->update($model, $attributes);
    }

    /**
     * Elimina un registro.
     */
    public function delete(Model $model): bool
    {
        return $this->repository->delete($model);
    }
}
