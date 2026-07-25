<?php

declare(strict_types=1);

namespace App\Core\Generator\Security;

use App\Core\Generator\DTO\ModuleData;

final class PermissionResolver
{
    /**
     * Construye permisos CRUD del módulo.
     *
     * @return array<int,string>
     */
    public function resolve(
        ModuleData $module
    ): array {

        $resource = $module->pluralVariable();

        return [

            "{$resource}.view",

            "{$resource}.create",

            "{$resource}.update",

            "{$resource}.delete",

        ];
    }
}
