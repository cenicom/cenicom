<?php

declare(strict_types=1);

namespace App\Core\Module\Bootstrap\Events;

final readonly class ModuleBootstrapping
{
    public function __construct(
        public int $modulesCount
    ) {
    }
}
