<?php

declare(strict_types=1);

namespace App\Core\Contracts\Module;


use App\Core\Module\DTO\ModuleDefinition;

interface ModuleDefinitionFactoryInterface
{
    public function create(
        string $manifestPath
    ): ModuleDefinition;
}
