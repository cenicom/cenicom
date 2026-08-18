<?php

declare(strict_types=1);

namespace App\Core\Generator\Generators;

use App\Core\Generator\BaseGenerator;
use App\Core\Generator\Builders\ModuleManifestBuilder;
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
 * Genera automáticamente el manifiesto de un módulo.
 *
 * El manifiesto contiene la definición estructural,
 * configuración y capacidades del módulo generado.
 *
 * @package App\Core\Generator\Generators
 * @since 1.0.0
 */
final class ModuleManifestGenerator extends BaseGenerator
{
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
        private readonly ModuleManifestBuilder $builder,
    ) {
        parent::__construct(
            $stubManager,
            $fileWriter,
            $presentationFactory,
            $validator,
        );
    }


    /**
     * Genera el manifiesto del módulo.
     */
    public function generate(ModuleData $module): GeneratorResult
    {

        $content = $this->render(
            'module-manifest.stub',
            $this->builder->build($module)
        );


        $this->write(
            $module->moduleManifestPath(),
            $content
        );


        return (new GeneratorResult())
            ->addCreated(
                $module->moduleManifestPath()
            );
    }
}
