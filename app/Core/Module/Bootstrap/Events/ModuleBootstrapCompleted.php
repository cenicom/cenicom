<?php

declare(strict_types=1);

namespace App\Core\Module\Bootstrap\Events;

final readonly class ModuleBootstrapCompleted
{
    public function __construct(
        public int $registeredModules,
        public int $failedModules
    ) {
    }
}
