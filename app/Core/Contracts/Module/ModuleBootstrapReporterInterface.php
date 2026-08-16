<?php

declare(strict_types=1);

namespace App\Core\Contracts\Module;

use App\Core\Module\DTO\ModuleDefinition;



interface ModuleBootstrapReporterInterface
{
    public function moduleDiscovered(string $manifest): void;

    public function moduleLoaded(ModuleDefinition $definition): void;

    public function moduleSkipped(
        ?ModuleDefinition $definition,
        string $reason
    ): void;

    public function moduleFailed(
        string $manifest,
        \Throwable $exception
    ): void;
}
