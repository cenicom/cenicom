<?php

declare(strict_types=1);

namespace App\Core\Actions\Contracts;

interface ActionInterface
{
    /**
     * Ejecuta el caso de uso.
     */
    public function execute(mixed ...$arguments): mixed;
}
