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

        $path = $module->serviceInterfacePath();

        logger($path);

        $created = $this->generateFile(
            self::STUB,
            $path,
            $this->buildVariables($module)
        );

        $result = new GeneratorResult();

        if ($created) {
            $result->addCreated($path);
        } else {
            $result->addSkipped($path);
        }

        return $result;
    }


    private function buildVariables(
        ModuleData $module
    ): array {

        return [

            'namespace' => $module->contractNamespace(),
            'qualifiedModel' => $module->qualifiedModel(),
            'serviceInterface' => $module->serviceInterface(),
            'model' => $module->modelClass(),
            'variable' => $module->variable(),

        ];
    }
}
