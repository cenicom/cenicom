<?php

declare(strict_types=1);

namespace App\Core\Security\Permissions\Contracts;

use App\Core\Security\Permissions\DTO\PermissionDefinition;

interface PermissionRegistrarInterface
{
    /**
     * Registra un permiso dentro del sistema.
     */
    public function register(
        string $name,
        string $description = '',
        ?string $module = null,
    ): PermissionDefinition;
}
