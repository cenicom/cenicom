<?php

declare(strict_types=1);

namespace App\Core\Navigation\Authorization\Contracts;


use App\Core\Navigation\DTO\NavigationItemData;

interface NavigationPermissionResolverInterface
{
    public function canView(
        NavigationItemData $item,
        ?string $permission,
    ): bool;
}
