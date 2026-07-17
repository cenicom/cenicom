<?php

declare(strict_types=1);

namespace App\Core\Generator\Generators;

use App\Core\Generator\BaseGenerator;
use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\Results\GeneratorResult;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Genera automáticamente el repositorio de un módulo.
 *
 * Procesa el stub correspondiente utilizando la información
 * contenida en ModuleData y persiste el resultado mediante
 * la infraestructura común del CN Generator.
 *
 * @package App\Core\Generator\Generators
 * @since 1.0.0
 */
final class RepositoryGenerator extends BaseGenerator
{
    private const STUB = 'repository.stub';
    /**
     * Determina si el generador aplica al módulo recibido.
     */
    public function supports(ModuleData $module): bool
    {
        return true;
    }

    /**
     * Genera el repositorio del módulo.
     */

    public function generate(
        ModuleData $module
    ): GeneratorResult {

        $path = $module->repositoryPath();

        $result = new GeneratorResult();

        if (
            $this->generateFile(
                self::STUB,
                $path,
                $this->buildVariables($module)
            )
        ) {
            $result->addCreated($path);
        } else {
            $result->addSkipped($path);
        }

        return $result;
    }

    /**
     * Construye las variables utilizadas por el stub.
     *
     * @return array<string, string>
     */
    private function buildVariables(
        ModuleData $module
    ): array {

        return [

            'namespace'
            => $module->repositoryNamespace(),

            'qualifiedModel'
            => $module->qualifiedModel(),

            'qualifiedRepositoryInterface'
            => $module->qualifiedRepositoryInterface(),

            'repositoryInterface'
            => $module->repositoryInterface(),

            'model'
            => $module->modelClass(),

            'repository'
            => $module->repositoryClass(),

            'variable'
            => $module->variable(),
        ];
    }
}
