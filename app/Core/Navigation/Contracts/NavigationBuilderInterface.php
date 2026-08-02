<?php

declare(strict_types=1);

namespace App\Core\Navigation\Contracts;

use App\Core\Navigation\DTO\NavigationTreeData;
use App\Core\Security\Contracts\IdentityInterface;

interface NavigationBuilderInterface
{
    public function build(
        IdentityInterface $identity,
    ): NavigationTreeData;
}
