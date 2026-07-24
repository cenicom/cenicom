<?php

declare(strict_types=1);

namespace App\Core\Generator\Generators;

use App\Core\Generator\BaseGenerator;
use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\Presentation\Factory\PresentationFactory;
use App\Core\Generator\Results\GeneratorResult;
use App\Core\Generator\Support\FileWriter;
use App\Core\Generator\Support\Permissions\PermissionBuilder;
use App\Core\Generator\Support\StubManager;
use App\Core\Generator\Validation\GeneratorValidator;

final class PermissionGenerator extends BaseGenerator
{
    public function __construct(
        StubManager $stubManager,
        FileWriter $fileWriter,
        PresentationFactory $presentationFactory,
        GeneratorValidator $validator,
        private readonly PermissionBuilder $permissionBuilder,
    ) {
        parent::__construct(
            $stubManager,
            $fileWriter,
            $presentationFactory,
            $validator,
        );
    }

    /**
     * Determina si aplica al módulo.
     */
    public function supports(ModuleData $module): bool
    {
        return $module->permissions()['permissions'] ?? true;
    }

    /**
     * Genera la clase de permisos.
     */
    public function generate(ModuleData $module): GeneratorResult
    {

        return $this->generateResult(
            'permission.stub',
            $module->permissionPath(),
            $this->permissionBuilder->build($module)
        );
    }
}
