<?php

declare(strict_types=1);

namespace App\Modules\Campus\Security;

use App\Core\Security\Permissions\Contracts\PermissionDefinitionInterface;
use App\Core\Security\Permissions\Contracts\PermissionRegistrarInterface;

final readonly class CampusPermissionDefinition
    implements PermissionDefinitionInterface
{
    public function register(
        PermissionRegistrarInterface $permissions
    ): void {
        $permissions->register(
            name: 'campuses.view',
            description: 'Permite consultar sedes educativas.',
            module: 'campus',
        );

        $permissions->register(
            name: 'campuses.create',
            description: 'Permite crear sedes educativas.',
            module: 'campus',
        );
    }
}
