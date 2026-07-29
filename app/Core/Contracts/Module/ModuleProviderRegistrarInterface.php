<?php

declare(strict_types=1);

namespace App\Core\Contracts\Module;

use App\Core\Module\DTO\ModuleDefinition;

interface ModuleProviderRegistrarInterface
{
    public function registerDefinition(
        ModuleDefinition $definition
    ): void;
}
