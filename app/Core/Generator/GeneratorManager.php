<?php

declare(strict_types=1);

namespace App\Core\Generator;

use App\Core\Generator\Contracts\GeneratorInterface;
use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\Results\GeneratorResult;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Corazón del CN Generator.
 *
 * Orquesta la ejecución de todos los generadores registrados
 * para producir automáticamente un módulo completo.
 *
 * El GeneratorManager no conoce implementaciones concretas;
 * únicamente trabaja mediante el contrato GeneratorInterface.
 *
 * Esto permite incorporar nuevos generadores sin modificar
 * este componente.
 *
 * @package App\Core\Generator
 * @since 1.0.0
 */
final class GeneratorManager
{
    /**
 * @param iterable<GeneratorInterface> $generators
 */
    public function __construct(
        private readonly iterable $generators,
    ) {
    }

    /**
     * Ejecuta todos los generadores compatibles con el módulo.
     */
    public function execute(
        ModuleData $module
    ): GeneratorResult {

        $result = new GeneratorResult();

        foreach ($this->generators as $generator) {

            if (! $generator->supports($module)) {
                continue;
            }

            try {

                $result->merge(
                    $generator->generate($module)
                );

            } catch (\Throwable $exception) {

                $result->addError(
                    sprintf(
                        '%s: %s',
                        $generator::class,
                        $exception->getMessage()
                    )
                );

            }

        }

        return $result;
    }
}
