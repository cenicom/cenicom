<?php

declare(strict_types=1);

namespace App\Core\Generator\Generators;


use App\Core\Generator\BaseGenerator;
use App\Core\Generator\Builders\ModelBuilder;
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
 * Genera automáticamente el modelo Eloquent de un módulo.
 *
 * Procesa el stub correspondiente utilizando la información
 * contenida en ModuleData y persiste el resultado mediante
 * FileWriter.
 *
 * @package App\Core\Generator\Generators
 * @since 1.0.0
 */
final class ModelGenerator extends BaseGenerator
{
    private const STUB = 'model.stub';

    public function supports(ModuleData $module): bool
    {
        return true;
    }

    public function __construct(
        StubManager $stubManager,
        FileWriter $fileWriter,
        PresentationFactory $presentationFactory,
        GeneratorValidator $validator,
        private readonly ModelBuilder $modelBuilder,
    ) {
        parent::__construct(
            $stubManager,
            $fileWriter,
            $presentationFactory,
            $validator,
        );
    }

    /**
     * Genera el modelo del módulo.
     */
    public function generate(ModuleData $module): GeneratorResult
    {
        return $this->generateResult(
            self::STUB,
            $module->modelPath(),
            array_merge(
                $this->defaultVariables($module),
                $this->modelBuilder->build($module)
            )
        );
    }


}
