<?php

declare(strict_types=1);

namespace App\Core\Repositories;

use App\Core\Contracts\RepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Repositorio base para todas las entidades del sistema.
 *
 * Centraliza las operaciones CRUD comunes utilizando Eloquent.
 *
 * @package App\Core\Repositories
 * @since 1.0.0
 */
abstract class BaseRepository implements RepositoryInterface
{
    /**
     * Modelo asociado al repositorio.
     */
    protected Model $model;

    /**
     * Retorna una nueva consulta del modelo.
     */
    protected function query(): Builder
    {
        return $this->model->newQuery();
    }

    /**
     * Pagina los registros.
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->query()->paginate($perPage);
    }

    /**
     * Busca un registro por su llave primaria.
     */
    public function findById(int|string $id): ?Model
    {
        return $this->query()->find($id);
    }

    /**
     * Crea un registro.
     */
    public function create(array $attributes): Model
    {
        return $this->query()->create($attributes);
    }

    /**
     * Actualiza un registro.
     */
    public function update(Model $model, array $attributes): Model
    {
        $model->update($attributes);

        return $model->refresh();
    }

    /**
     * Elimina un registro.
     */
    public function delete(Model $model): bool
    {
        return (bool) $model->delete();
    }
}
