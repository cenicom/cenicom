<?php

declare(strict_types=1);

namespace App\Core\Security\Policies;

use App\Core\Security\Policies\Contracts\PolicyInterface;
use App\Core\Security\Policies\Contracts\PolicyRegistryInterface;
use App\Core\Security\Policies\Contracts\PolicyResolverInterface;

final class PolicyResolver implements PolicyResolverInterface
{
    public function __construct(
        private readonly PolicyRegistryInterface $registry,
    ) {
    }

    public function resolve(
        string $name
    ): ?PolicyInterface {
        return $this->registry->policy($name);
    }
}
