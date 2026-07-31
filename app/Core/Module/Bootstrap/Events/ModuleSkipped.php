<?php

declare(strict_types=1);

namespace App\Core\Module\Bootstrap\Events;

final readonly class ModuleSkipped
{
    public function __construct(
        public string $moduleName,
        public string $reason
    ) {
    }
}
