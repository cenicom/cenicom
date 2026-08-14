<?php

declare(strict_types=1);

namespace App\Core\Security\Policies\Contracts;

interface PolicyResolverInterface
{
    /**
     * Resuelve una Policy registrada bajo una clave.
     */
    public function resolve(
        string $name
    ): ?PolicyInterface;
}
