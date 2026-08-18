<?php

declare(strict_types=1);

namespace App\Core\Generator\Builders;

use App\Core\Generator\DTO\ModuleData;

final class RepositoryInterfaceBuilder
{
    /**
     * @return array<string,string>
     */
    public function build(ModuleData $module): array
    {
        return [
            'namespace' => $module->repositoryContractNamespace(),

            'repositoryInterface' => $module->repositoryInterface(),

            'qualifiedModel' => $module->qualifiedModel(),

            'model' => $module->modelClass(),

            'variable' => $module->variable(),
        ];
    }
}
