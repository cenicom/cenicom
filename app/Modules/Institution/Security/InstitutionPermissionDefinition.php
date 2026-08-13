<?php

declare(strict_types=1);

namespace App\Modules\Institution\Security;

use App\Core\Security\Permissions\Contracts\PermissionDefinitionInterface;
use App\Core\Security\Permissions\Contracts\PermissionRegistrarInterface;

final readonly class InstitutionPermissionDefinition
    implements PermissionDefinitionInterface
{
    public function register(
        PermissionRegistrarInterface $permissions
    ): void {
        $permissions->register(
            name: 'institutions.view',
            description: 'Permite consultar instituciones.',
            module: 'institution',
        );

        $permissions->register(
            name: 'institutions.create',
            description: 'Permite crear instituciones.',
            module: 'institution',
        );

        $permissions->register(
            name: 'institutions.update',
            description: 'Permite actualizar instituciones.',
            module: 'institution',
        );

        $permissions->register(
            name: 'institutions.delete',
            description: 'Permite eliminar instituciones.',
            module: 'institution',
        );
    }
}
