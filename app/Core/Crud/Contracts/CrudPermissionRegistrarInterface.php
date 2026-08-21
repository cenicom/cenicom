<?php

declare(strict_types=1);

namespace App\Core\Crud\Contracts;

interface CrudPermissionRegistrarInterface
{
    /**
     * Registra los permisos CRUD de un recurso.
     *
     * @return array<int, string> Nombres de permisos registrados.
     */
    public function register(
        string $resource,
        ?string $module = null,
    ): array;
}
