<?php

declare(strict_types=1);

namespace App\Core\Generator\Generators;

use App\Core\Generator\BaseGenerator;
use App\Core\Generator\DTO\ModuleData;
use App\Core\Generator\Results\GeneratorResult;

final class ServiceInterfaceGenerator extends BaseGenerator
{
    private const STUB = 'service-interface.stub';


    public function supports(ModuleData $module): bool
    {
        return true;
    }


    public function generate(
        ModuleData $module
    ): GeneratorResult {

        logger('=== ServiceInterfaceGenerator ejecutado ===');
        logger($module->serviceInterfacePath());

        return $this->generateResult(
            self::STUB,
            $module->serviceInterfacePath(),
            $this->buildVariables($module)
        );
    }


    private function buildVariables(
        ModuleData $module
    ): array {

        return [

            'namespace' => $module->serviceContractNamespace(),

            'qualifiedModel' => $module->qualifiedModel(),

            'serviceInterface' => $module->serviceInterface(),

            'model' => $module->modelClass(),

            'variable' => $module->variable(),

        ];
    }
}
