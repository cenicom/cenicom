<?php

declare(strict_types=1);

namespace App\Core\Generator\Builders;

use App\Core\Generator\DTO\ModuleData;

final class ActionBuilder
{
    /**
     * @return array<string,string>
     */
    public function build(ModuleData $module): array
    {
        return [
            'namespace' => $module->actionNamespace(),

            'action' => $module->actionClass(),

            'qualifiedServiceInterface'
                => $module->qualifiedServiceInterface(),

            'serviceInterface'
                => $module->serviceInterface(),

            'qualifiedModel'
                => $module->qualifiedModel(),

            'model'
                => $module->modelClass(),

            'variable'
                => $module->variable(),
        ];
    }
}
