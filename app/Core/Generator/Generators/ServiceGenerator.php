<?php

declare(strict_types=1);

namespace App\Core\Generator\Generators;

use App\Core\Generator\BaseGenerator;
use App\Core\Generator\Builders\ServiceBuilder;
use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\Results\GeneratorResult;
use App\Core\Generator\Presentation\Factory\PresentationFactory;
use App\Core\Generator\Support\FileWriter;
use App\Core\Generator\Support\StubManager;
use App\Core\Generator\Validation\GeneratorValidator;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Genera automáticamente el servicio de un módulo.
 *
 * Procesa el stub correspondiente utilizando la información
 * contenida en ModuleData y persiste el resultado mediante
 * la infraestructura común del CN Generator.
 *
 * @package App\Core\Generator\Generators
 * @since 1.0.0
 */
final class ServiceGenerator extends BaseGenerator
{
    private const STUB = 'service.stub';

    private readonly ServiceBuilder $builder;

    public function __construct(
        StubManager $stubManager,
        FileWriter $fileWriter,
        PresentationFactory $presentationFactory,
        GeneratorValidator $validator,
        ?ServiceBuilder $builder = null,
    ) {
        parent::__construct(
            $stubManager,
            $fileWriter,
            $presentationFactory,
            $validator,
        );

        $this->builder = $builder ?? new ServiceBuilder();
    }

    /**
     * Determina si el generador aplica al módulo recibido.
     */
    public function supports(ModuleData $module): bool
    {
        return true;
    }

    /**
     * Genera el servicio del módulo.
     */
    public function generate(ModuleData $module): GeneratorResult
    {
        return $this->generateResult(
            self::STUB,
            $module->servicePath(),
            $this->buildVariables($module)
        );
    }

    /**
     * Construye las variables utilizadas por el stub.
     *
     * @return array<string,mixed>
     */
    private function buildVariables(ModuleData $module): array
    {
        return array_merge(
            $this->defaultVariables($module),
            $this->builder->build($module),
        );
    }
}
