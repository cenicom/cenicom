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
 * Genera automáticamente la Policy de un módulo.
 *
 * Procesa el stub correspondiente utilizando la información
 * contenida en ModuleData y persiste el resultado mediante
 * la infraestructura común del CN Generator.
 *
 * @package App\Core\Generator\Generators
 * @since 1.0.0
 */
final class PolicyGenerator extends BaseGenerator
{

    /**
     * Determina si el generador aplica al módulo recibido.
     */
    public function supports(ModuleData $module): bool
    {
        return true;
    }

    /**
     * Genera la Policy del módulo.
     */
    public function generate(
        ModuleData $module
    ): GeneratorResult {

        $content = $this->render(
            'policy.stub',
            $this->buildVariables($module)
        );

        $this->write(
            $module->policyPath(),
            $content
        );

        return (new GeneratorResult())
            ->addCreated(
                $module->policyPath()
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

            'namespace'
            => $module->policyNamespace(),

            'policy'
            => $module->policyClass(),

            'modelNamespace'
            => $module->modelNamespace(),

            'model'
            => $module->modelClass(),

            'qualifiedModel'
            => $module->qualifiedModel(),

            'variable'
            => lcfirst($module->modelClass()),
        ];
    }
}
