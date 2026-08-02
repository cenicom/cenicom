<?php

declare(strict_types=1);

namespace App\Core\Navigation\Contracts;

use App\Core\Navigation\DTO\NavigationTreeData;

interface NavigationActiveResolverInterface
{
    public function resolve(
        NavigationTreeData $tree
    ): NavigationTreeData;
}
