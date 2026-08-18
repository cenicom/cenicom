<?php

declare(strict_types=1);

namespace App\Core\Generator\Builders;

use App\Core\Generator\DTO\ModuleData;

final class FactoryBuilder
{
    /**
     * @return array<string,string>
     */
    public function build(ModuleData $module): array
    {
        return [
            'namespace' => $module->factoryNamespace(),
            'factory' => $module->factoryClass(),
            'modelNamespace' => $module->modelNamespace(),
            'model' => $module->modelClass(),
            'qualifiedModel' => $module->qualifiedModel(),
        ];
    }
}
