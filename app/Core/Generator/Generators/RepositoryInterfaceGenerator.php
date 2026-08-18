<?php

declare(strict_types=1);

namespace App\Core\Generator\Generators;

use App\Core\Generator\BaseGenerator;
use App\Core\Generator\Builders\RepositoryInterfaceBuilder;
use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\Presentation\Factory\PresentationFactory;
use App\Core\Generator\Results\GeneratorResult;
use App\Core\Generator\Support\FileWriter;
use App\Core\Generator\Support\StubManager;
use App\Core\Generator\Validation\GeneratorValidator;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Genera automáticamente la interfaz del repositorio
 * de un módulo.
 *
 * Procesa el stub correspondiente utilizando la información
 * contenida en ModuleData y persiste el resultado mediante
 * la infraestructura común del CN Generator.
 *
 * @package App\Core\Generator\Generators
 * @since 1.0.0
 */
final class RepositoryInterfaceGenerator extends BaseGenerator
{
    private readonly RepositoryInterfaceBuilder $builder;

    /**
     * Stub utilizado para la generación.
     */
    private const STUB = 'repository-interface.stub';

    /**
     * Determina si el generador aplica al módulo recibido.
     */
    public function supports(ModuleData $module): bool
    {
        return true;
    }

    public function __construct(
        StubManager $stubManager,
        FileWriter $fileWriter,
        PresentationFactory $presentationFactory,
        GeneratorValidator $validator,
        RepositoryInterfaceBuilder $builder,
    ) {
        parent::__construct(
            $stubManager,
            $fileWriter,
            $presentationFactory,
            $validator,
        );

        $this->builder = $builder;
    }

    /**
     * Genera la interfaz del repositorio.
     */
    public function generate(ModuleData $module): GeneratorResult
    {

        return $this->generateResult(
            self::STUB,
            $module->repositoryInterfacePath(),
            $this->builder->build($module)
        );
    }
}
