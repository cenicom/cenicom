<?php

declare(strict_types=1);

namespace App\Core\Generator\Contracts;

use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\Results\GeneratorResult;


/**
 * Contrato base para todos los generadores del CN Generator.
 *
 * Cada implementación es responsable de generar un único tipo de
 * artefacto (modelo, controlador, vista, migración, etc.).
 *
 * El ModuleGenerator utilizará este contrato para ejecutar los
 * generadores de forma desacoplada y extensible.
 */
interface GeneratorInterface
{
    /**
     * Determina si el generador aplica al módulo recibido.
     */
    public function supports(ModuleData $module): bool;

    /**
    * Genera el artefacto correspondiente y devuelve
    * el resultado de la operación.
    */
    public function generate(ModuleData $module): GeneratorResult;
}
