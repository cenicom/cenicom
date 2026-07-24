<?php

declare(strict_types=1);

namespace App\Core\Generator\Generators;

use App\Core\Generator\BaseGenerator;
use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\Presentation\Factory\PresentationFactory;
use App\Core\Generator\Results\GeneratorResult;
use App\Core\Generator\Support\FileWriter;
use App\Core\Generator\Support\Middleware\MiddlewareBuilder;
use App\Core\Generator\Support\StubManager;
use App\Core\Generator\Validation\GeneratorValidator;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Genera automáticamente el Middleware del módulo.
 *
 * Responsabilidades:
 *
 * - Validar si aplica al módulo.
 * - Delegar la construcción de variables al MiddlewareBuilder.
 * - Generar el archivo mediante BaseGenerator.
 *
 * Toda la lógica de construcción pertenece al Builder.
 */
final class MiddlewareGenerator extends BaseGenerator
{
    /**
     * Constructor.
     */
    public function __construct(
        StubManager $stubManager,
        FileWriter $fileWriter,
        PresentationFactory $presentationFactory,
        GeneratorValidator $validator,
        private readonly MiddlewareBuilder $middlewareBuilder,
    ) {
        parent::__construct(
            $stubManager,
            $fileWriter,
            $presentationFactory,
            $validator,
        );
    }

    /**
     * Determina si el generador aplica al módulo.
     */
    public function supports(ModuleData $module): bool
    {

        return true;
    }

    /**
     * Genera el Middleware del módulo.
     */
    public function generate(ModuleData $module): GeneratorResult
    {

        return $this->generateResult(
            'middleware.stub',
            $module->middlewarePath(),
            $this->middlewareBuilder->build($module),
        );
    }
}
