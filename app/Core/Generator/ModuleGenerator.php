<?php

declare(strict_types=1);

namespace App\Core\Generator;



use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\Results\GeneratorResult;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Fachada principal del CN Generator.
 *
 * Proporciona un punto único de entrada para la generación
 * automática de módulos del ERP.
 *
 * Delega la orquestación al GeneratorManager, manteniendo
 * desacoplado al resto de la aplicación del funcionamiento
 * interno del motor de generación.
 *
 * @package App\Core\Generator
 * @since 1.0.0
 */
final readonly class ModuleGenerator
{


    private array $generators;

    public function __construct(
        array $generators
    ) {
        $this->generators = $generators;
    }

    /**
     * Genera un módulo completo.
     */
    public function generate(
    ModuleData $module
): GeneratorResult
{
    $result = new GeneratorResult();

    foreach ($this->generators as $generator) {

        $generatorResult = $generator->generate($module);

        $result->merge($generatorResult);
    }

    return $result;
}
}
