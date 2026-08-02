<?php

declare(strict_types=1);

namespace App\Core\Navigation\Discovery\Contracts;

interface NavigationDiscoveryRegistrarInterface
{
    /**
     * Descubre y registra automáticamente
     * todos los manifiestos de navegación.
     */
    public function register(): void;
}
