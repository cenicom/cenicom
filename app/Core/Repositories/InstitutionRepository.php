<?php

declare(strict_types=1);

namespace App\Core\Repositories;

use App\Models\{{ model }};
use {{ contractNamespace }}\{{ repositoryInterface }};

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Repositorio de {{ model }}.
 *
 * Encapsula el acceso a datos del modelo y centraliza
 * todas las operaciones de persistencia.
 *
 * @package App\Core\Repositories
 */
final readonly class {{ repository }}
    implements {{ repositoryInterface }}
{
    public function __construct(
        private {{ model }} $model,
    ) {
    }

    /**
     * Obtiene todos los registros.
     */
    public function all()
    {
        return $this->model
            ->query()
            ->get();
    }

    /**
     * Busca un registro por su ID.
     */
    public function find(int|string $id): ?{{ model }}
    {
        return $this->model
            ->query()
            ->find($id);
    }

    /**
     * Crea un registro.
     *
     * @param array<string,mixed> $data
     */
    public function create(array $data): {{ model }}
    {
        return $this->model
            ->query()
            ->create($data);
    }

    /**
     * Actualiza un registro.
     *
     * @param array<string,mixed> $data
     */
    public function update(
        {{ model }} $model,
        array $data
    ): bool {
        return $model->update($data);
    }

    /**
     * Elimina un registro.
     */
    public function delete(
        {{ model }} $model
    ): bool {
        return (bool) $model->delete();
    }
}
