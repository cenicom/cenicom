<?php

declare(strict_types=1);

namespace App\Core\Contracts;

use App\Core\Navigation\DTO\NavigationNodeData;

interface NavigationAuthorizationInterface
{
    public function allows(
        NavigationNodeData $node
    ): bool;
}
