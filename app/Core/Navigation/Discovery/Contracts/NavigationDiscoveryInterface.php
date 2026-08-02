<?php

declare(strict_types=1);

namespace App\Core\Navigation\Discovery\Contracts;

interface NavigationDiscoveryInterface
{
    /**
     * Descubre todos los manifiestos de navegación.
     *
     * @return list<string>
     */
    public function discover(): array;
}
