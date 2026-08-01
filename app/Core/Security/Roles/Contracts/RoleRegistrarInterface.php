<?php

declare(strict_types=1);

namespace App\Core\Security\Roles\Contracts;

use App\Core\Security\Roles\DTO\RoleDefinition;

interface RoleRegistrarInterface
{
    /**
     * Registra un rol en el sistema.
     *
     * @param array<int, string> $permissions
     */
    public function register(
        string $name,
        string $label,
        array $permissions = []
    ): RoleDefinition;
}
