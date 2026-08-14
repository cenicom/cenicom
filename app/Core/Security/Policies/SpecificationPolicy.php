<?php

declare(strict_types=1);

namespace App\Core\Security\Policies;

use App\Core\Security\Contracts\IdentityInterface;
use App\Core\Security\Policies\Contracts\SpecificationPolicyInterface;
use App\Core\Specification\Contracts\SpecificationInterface;

final readonly class SpecificationPolicy implements SpecificationPolicyInterface
{
    public function __construct(
        private SpecificationInterface $specification,
    ) {
    }

    public function allows(
        IdentityInterface $identity,
        mixed $resource
    ): bool {
        return $this->specification->isSatisfiedBy(
            $identity
        );
    }
}
