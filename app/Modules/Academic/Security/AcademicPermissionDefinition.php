<?php

declare(strict_types=1);

namespace App\Modules\Academic\Security;

use App\Core\Security\Permissions\Contracts\PermissionDefinitionInterface;
use App\Core\Security\Permissions\Contracts\PermissionRegistrarInterface;

final readonly class AcademicPermissionDefinition implements PermissionDefinitionInterface
{
    public function register(PermissionRegistrarInterface $permissions): void
    {
        $permissions->register(
            'academic_year.create',
            'Crear un año lectivo en borrador con una versión oficial compatible.',
            'academic',
        );

        $permissions->register(
            'academic_year.activate',
            'Activar un año lectivo en borrador.',
            'academic',
        );

        $permissions->register(
            'academic_year.close',
            'Cerrar un año lectivo después de su fin oficial y completar prerrequisitos.',
            'academic',
        );

        $permissions->register(
            'academic_year.complete_milestone',
            'Registrar el cumplimiento de un hito académico requerido.',
            'academic',
        );

        $permissions->register(
            'academic_year.cancel',
            'Cancelar un año lectivo en borrador con justificación obligatoria y auditoría.',
            'academic',
        );
    }
}
