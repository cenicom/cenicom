<?php

declare(strict_types=1);

namespace App\Core\Generator\Builders;

use App\Core\Generator\DTO\ModuleData;

final class SeederBuilder
{
    /**
     * @return array<string,string>
     */
    public function build(ModuleData $module): array
    {
        return [
            'seeder' => $module->seederClass(),

            'model' => $module->modelClass(),

            'namespace' => $module->seederNamespace(),

            'class' => $module->seederClass(),

            'modelNamespace' => $module->modelNamespace(),

            'modelClass' => $module->modelClass(),

            'factoryClass' => $module->factoryClass(),

            'qualifiedModel' => $module->qualifiedModel(),

            'qualifiedFactory' => $module->qualifiedFactory(),
        ];
    }
}
