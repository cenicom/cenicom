<?php

declare(strict_types=1);

namespace App\Core\Security\Policies\Contracts;

use App\Core\Security\Contracts\IdentityInterface;

interface PolicyInterface
{
    /**
     * Determina si la identidad está autorizada
     * para operar sobre el recurso indicado.
     */
    public function allows(
        IdentityInterface $identity,
        mixed $resource
    ): bool;
}
