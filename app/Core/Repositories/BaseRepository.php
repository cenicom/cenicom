<?php

declare(strict_types=1);

namespace App\Core\Repositories;

use App\Core\Contracts\RepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Repositorio base para todos los módulos del sistema.
 *
 * Encapsula las operaciones comunes de persistencia
 * delegando la interacción con Eloquent.
 *
 * @package App\Core\Repositories
 * @since 1.0.0
 */
abstract class BaseRepository implements RepositoryInterface
{
    public function __construct(
        protected Model $model
    ) {
    }

    /**
     * Obtiene un nuevo constructor de consultas del modelo.
     */
    protected function query(): Builder
    {
        return $this->model->newQuery();
    }

    /**
     * Obtiene registros paginados.
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->query()
            ->paginate($perPage);
    }

    /**
     * Obtiene todos los registros.
     */
    public function all(): Collection
    {
        return $this->query()
            ->get();
    }

    /**
     * Busca un registro por identificador.
     */
    public function findById(
        int|string $id,
        array $columns = ['*']
    ): ?Model {
        return $this->query()
            ->find($id, $columns);
    }

    /**
     * Busca un registro o lanza una excepción.
     */
    public function findOrFail(
        int|string $id,
        array $columns = ['*']
    ): Model {
        return $this->query()
            ->findOrFail($id, $columns);
    }

    /**
     * Crea un nuevo registro.
     */
    public function create(array $attributes): Model
    {
        return $this->query()
            ->create($attributes);
    }

    /**
     * Actualiza un registro existente.
     */
    public function update(
        int|string $id,
        array $attributes
    ): bool {
        return $this->findOrFail($id)
            ->update($attributes);
    }

    /**
     * Elimina un registro.
     */
    public function delete(int|string $id): bool
    {
        return (bool) $this->findOrFail($id)
            ->delete();
    }

    /**
     * Restaura un registro eliminado.
     */
    public function restore(int|string $id): bool
    {
        return (bool) $this->query()
            ->onlyTrashed()
            ->findOrFail($id)
            ->restore();
    }

    /**
     * Elimina definitivamente un registro.
     */
    public function forceDelete(int|string $id): bool
    {
        return (bool) $this->query()
            ->onlyTrashed()
            ->findOrFail($id)
            ->forceDelete();
    }

    /**
     * Verifica si existe un registro.
     */
    public function exists(int|string $id): bool
    {
        return $this->query()
            ->whereKey($id)
            ->exists();
    }

    /**
     * Obtiene la cantidad total de registros.
     */
    public function count(): int
    {
        return $this->query()
            ->count();
    }
}
