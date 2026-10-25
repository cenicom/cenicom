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
            'institution_academic_profile.configure',
            'Configurar las dimensiones académicas y el calendario A/B de una institución.',
            'academic',
        );

        $permissions->register(
            'official_calendar_publication.confirm_identity',
            'Confirmar la identidad canónica de una publicación de calendario oficial.',
            'academic',
        );

        $permissions->register(
            'official_calendar_version.validate',
            'Validar una versión en borrador de un calendario oficial.',
            'academic',
        );

        $permissions->register(
            'official_calendar_version.withdraw',
            'Retirar una versión validada o disponible de un calendario oficial.',
            'academic',
        );

        $permissions->register(
            'official_calendar_version.publish',
            'Publicar una versión validada de un calendario oficial.',
            'academic',
        );

        $permissions->register(
            'official_calendar_version.correct',
            'Crear una nueva versión corregida de un calendario oficial.',
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
