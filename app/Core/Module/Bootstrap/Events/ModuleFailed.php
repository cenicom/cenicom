<?php

declare(strict_types=1);

namespace App\Core\Module\Bootstrap\Events;

final readonly class ModuleFailed
{
    public function __construct(
        public string $moduleName,
        public \Throwable $exception
    ) {
    }
}
