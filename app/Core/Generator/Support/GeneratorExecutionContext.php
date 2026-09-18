<?php

declare(strict_types=1);

namespace App\Core\Generator\Support;

final class GeneratorExecutionContext
{
    private bool $force = false;

    public function force(): bool
    {
        return $this->force;
    }

    public function setForce(bool $force): void
    {
        $this->force = $force;
    }
}
