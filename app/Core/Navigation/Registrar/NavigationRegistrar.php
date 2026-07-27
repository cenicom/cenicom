<?php

declare(strict_types=1);

namespace App\Core\Navigation\Registrar;

use App\Core\Navigation\Contracts\NavigationRegistrarInterface;
use App\Core\Navigation\Contracts\NavigationRegistryInterface;
use App\Core\Navigation\DTO\NavigationGroupData;
use App\Core\Navigation\DTO\NavigationItemData;


final readonly class NavigationRegistrar implements NavigationRegistrarInterface
{
    public function __construct(
        private NavigationRegistryInterface $registry
    ) {
    }

    /**
     * Registra un grupo.
     */
    public function group(
        NavigationGroupData $group
    ): self {
        $this->registry->registerGroup($group);

        return $this;
    }

    /**
     * Registra un elemento.
     */
    public function item(
        NavigationItemData $item
    ): self {
        $this->registry->registerItem($item);

        return $this;
    }
}
