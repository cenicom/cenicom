<?php

declare(strict_types=1);

namespace App\Core\Security\Policies;

use App\Core\Security\Contracts\IdentityInterface;
use App\Core\Security\Policies\Contracts\PolicyInterface;
use App\Core\Security\Policies\Contracts\ResourceSpecificationInterface;

final readonly class ResourceSpecificationPolicy implements PolicyInterface
{
    public function __construct(
        private ResourceSpecificationInterface $specification,
    ) {
    }

    /**
     * Determina si la identidad está autorizada
     * para operar sobre el recurso indicado.
     */
    public function allows(
        IdentityInterface $identity,
        mixed $resource
    ): bool {
        return $this->specification->isSatisfiedBy(
            $identity,
            $resource,
        );
    }
}
