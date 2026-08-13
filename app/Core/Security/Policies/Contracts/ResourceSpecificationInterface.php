<?php

declare(strict_types=1);

namespace App\Core\Security\Policies\Contracts;

use App\Core\Security\Contracts\IdentityInterface;

interface ResourceSpecificationInterface
{
    /**
     * Determina si una identidad satisface
     * una regla respecto a un recurso.
     */
    public function isSatisfiedBy(
        IdentityInterface $identity,
        mixed $resource
    ): bool;
}
