<?php

declare(strict_types=1);

namespace App\Core\Services;

use App\Core\Contracts\RepositoryInterface;
use App\Core\Contracts\ServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Servicio base para todos los módulos del sistema.
 *
 * Centraliza las operaciones CRUD comunes delegándolas
 * al repositorio correspondiente.
 *
 * @package App\Core\Services
 * @since 1.0.0
 */
abstract class BaseService implements ServiceInterface
{
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
     * Obtiene todos los registros.
     */
    public function all(): Collection
    {
        return $this->repository->all();
    }

    /**
     * Busca un registro por su identificador.
     */
    public function findById(
        int|string $id,
        array $columns = ['*']
    ): ?Model {
        return $this->repository->findById($id, $columns);
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
    public function update(
        int|string $id,
        array $attributes
    ): bool {
        return $this->repository->update($id, $attributes);
    }

    public function delete(
        int|string $id
    ): bool {
        return $this->repository->delete($id);
    }

    /**
     * Restaura un registro eliminado.
     */
    public function restore(int|string $id): bool
    {
        return $this->repository->restore($id);
    }

    /**
     * Elimina definitivamente un registro.
     */
    public function forceDelete(int|string $id): bool
    {
        return $this->repository->forceDelete($id);
    }

    /**
     * Verifica si un registro existe.
     */
    public function exists(int|string $id): bool
    {
        return $this->repository->exists($id);
    }

    /**
     * Obtiene la cantidad total de registros.
     */
    public function count(): int
    {
        return $this->repository->count();
    }
}
