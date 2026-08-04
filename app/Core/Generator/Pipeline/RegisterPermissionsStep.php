<?php

declare(strict_types=1);

namespace App\Core\Generator\Pipeline;

use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\Pipeline\Contracts\PipelineStepInterface;
use App\Core\Generator\Results\GeneratorResult;
use App\Core\Security\Permissions\Contracts\PermissionRegistrarInterface;
use Closure;

/**
 * Registra los permisos del módulo dentro del PermissionRegistry.
 */
final readonly class RegisterPermissionsStep implements PipelineStepInterface
{
    public function __construct(
        private PermissionRegistrarInterface $registrar,
    ) {}

    /**
     * Registra la matriz de permisos y continúa el pipeline.
     */
    public function handle(
        ModuleData $module,
        GeneratorResult $result,
        Closure $next,
    ): GeneratorResult {

        $matrix = $module->permissionMatrix();

        if (count($matrix->permissions()) === 0) {
            return $next($module, $result);
        }

        foreach ($matrix->permissions() as $permission) {

            $this->registrar->register(
                $permission->permission(),
                $permission->description() ?? '',
                $permission->group(),
            );
        }

        return $next($module, $result);
    }
}
