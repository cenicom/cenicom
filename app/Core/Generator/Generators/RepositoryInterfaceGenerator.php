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
 * Genera automáticamente la interfaz del repositorio
 * de un módulo.
 *
 * Procesa el stub correspondiente utilizando la información
 * contenida en ModuleData y persiste el resultado mediante
 * la infraestructura común del CN Generator.
 *
 * @package App\Core\Generator\Generators
 * @since 1.0.0
 */
final class RepositoryInterfaceGenerator extends BaseGenerator
{
    /**
     * Stub utilizado para la generación.
     */
    private const STUB = 'repository-interface.stub';

    /**
     * Determina si el generador aplica al módulo recibido.
     */
    public function supports(ModuleData $module): bool
    {
        return true;
    }

    /**
     * Genera la interfaz del repositorio.
     */
    public function generate(
        ModuleData $module
    ): GeneratorResult {

        return $this->generateResult(
            self::STUB,
            $module->repositoryInterfacePath(),
            $this->buildVariables($module)
        );
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

        'namespace' => $module->repositoryContractNamespace(),

            'repositoryInterface'
            => $module->repositoryInterface(),

            'qualifiedModel'
            => $module->qualifiedModel(),

            'model'
            => $module->modelClass(),

            'variable'
            => $module->variable(),

        ];
    }
}
