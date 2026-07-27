<?php

declare(strict_types=1);

namespace App\Core\Navigation\Contracts;

use App\Core\Navigation\DTO\NavigationGroupData;
use App\Core\Navigation\DTO\NavigationItemData;


interface NavigationRegistrarInterface
{
    /**
     * Registra un grupo de navegación.
     */
    public function group(
        NavigationGroupData $group
    ): self;

    /**
     * Registra un elemento de navegación.
     */
    public function item(
        NavigationItemData $item
    ): self;
}
