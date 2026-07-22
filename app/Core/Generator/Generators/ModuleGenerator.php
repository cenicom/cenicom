<?php

declare(strict_types=1);

namespace App\Core\Generator\Generators;

use App\Core\Generator\Contracts\GeneratorInterface;
use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\Results\GeneratorResult;
use App\Core\Generator\Validation\GeneratorTestSuite;

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
    /**
     * @param iterable<GeneratorInterface> $generators
     */
    public function __construct(
        private readonly iterable $generators,
        private readonly GeneratorTestSuite $testSuite,
    ) {}

    /**
     * Ejecuta los generadores compatibles con el módulo.
     */
    public function generate(ModuleData $module): GeneratorResult
    {
        $result = new GeneratorResult();

        foreach ($this->generators as $generator) {

            if (! $generator->supports($module)) {
                continue;
            }

            $result->merge(
                $generator->generate($module)
            );
        }

        $this->testSuite->validate($result);

        return $result;
    }
}
