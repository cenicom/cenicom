<?php

declare(strict_types=1);

namespace App\Core\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface RepositoryInterface
{
    /**
     * Obtiene los registros paginados.
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator;

    /**
     * Obtiene todos los registros.
     */
    public function all(): Collection;

    /**
     * Busca un registro por su llave primaria.
     */
    public function findById(
        int|string $id,
        array $columns = ['*']
    ): ?Model;

    /**
     * Busca un registro por su llave primaria
     * o lanza una excepción si no existe.
     */
    public function findOrFail(
        int|string $id,
        array $columns = ['*']
    ): Model;

    /**
     * Crea un registro por sus atributos.
     */
    public function create(array $attributes): Model;

    /**
     * Actualiza un registro por su llave primaria.
     */
    public function update(
        int|string $id,
        array $attributes
    ): bool;

    /**
     * Elimina un registro por su llave primaria.
     */
    public function delete(int|string $id): bool;

    /**
     * Restaura un registro por su llave primaria.
     */
    public function restore(int|string $id): bool;

    /**
     * Elimina definitivamente un registro.
     */
    public function forceDelete(int|string $id): bool;

    /**
     * Verifica si existe un registro por su llave primaria.
     */
    public function exists(int|string $id): bool;

    /**
     * Cuenta los registros.
     */
    public function count(): int;
}
