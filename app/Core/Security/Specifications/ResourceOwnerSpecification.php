<?php

declare(strict_types=1);

namespace App\Core\Security\Specifications;

use App\Core\Security\Contracts\IdentityInterface;
use App\Core\Security\Policies\Contracts\OwnedResourceInterface;
use App\Core\Security\Policies\Contracts\ResourceSpecificationInterface;

final class ResourceOwnerSpecification
    implements ResourceSpecificationInterface
{
    public function isSatisfiedBy(
        IdentityInterface $identity,
        mixed $resource
    ): bool {
        if (! $resource instanceof OwnedResourceInterface) {
            return false;
        }

        $identityId = $identity->id();

        if ($identityId === null) {
            return false;
        }

        return $identityId === $resource->ownerId();
    }
}
