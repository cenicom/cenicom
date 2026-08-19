<?php

declare(strict_types=1);

namespace App\Core\Generator\Generators;

use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\Pipeline\Contracts\PipelineInterface;
use App\Core\Generator\Results\GeneratorResult;

/**
 * Orquesta la ejecución de todos los generadores del CN Generator.
 *
 * Recorre la colección de generadores registrados y ejecuta únicamente
 * aquellos que indiquen ser compatibles con el módulo mediante
 * supports().
 *
 * Esta clase no conoce detalles de implementación de ningún generador,
 * limitándose exclusivamente a coordinar su ejecución y consolidar
 * el resultado de la operación.
 */
final class ModuleGenerator
{

    public function __construct(
        private PipelineInterface $pipeline,
    ) {}

    /**
     * Ejecuta los generadores compatibles con el módulo.
     */
    public function generate(ModuleData $module): GeneratorResult
    {
        return $this->pipeline->process($module);
    }
}
