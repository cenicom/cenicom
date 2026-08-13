<?php

declare(strict_types=1);

namespace App\Core\Security\Permissions\Contracts;

interface PermissionDefinitionInterface
{
    /**
     * Registra los permisos del módulo.
     */
    public function register(
        PermissionRegistrarInterface $permissions
    ): void;
}
