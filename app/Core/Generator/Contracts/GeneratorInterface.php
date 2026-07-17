<?php

declare(strict_types=1);

namespace App\Core\Generator\Contracts;

use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\Results\GeneratorResult;


/**
 * Contrato base para todos los generadores del CN Generator.
 *
 * Cada implementación genera un único tipo de artefacto
 * (modelo, controlador, vista, migración, etc.).
 *
 * El GeneratorManager (Corazón del CN Generator)
 * utilizará este contrato para ejecutar los generadores
 * de forma desacoplada y extensible.
 */
interface GeneratorInterface
{
    /**
     *  Ejecuta la generación del artefacto y devuelve
        * el resultado completo de la operación.
     */
    public function supports(ModuleData $module): bool;

    /**
    * Genera el artefacto correspondiente y devuelve
    * el resultado de la operación.
    */
    public function generate(ModuleData $module): GeneratorResult;
}
