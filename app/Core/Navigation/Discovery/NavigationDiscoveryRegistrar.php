<?php

declare(strict_types=1);

namespace App\Core\Navigation\Discovery;

use App\Core\Navigation\Contracts\NavigationManifestLoaderInterface;
use App\Core\Navigation\Contracts\NavigationRegistrarInterface;
use App\Core\Navigation\Discovery\Contracts\NavigationDiscoveryInterface;
use App\Core\Navigation\Discovery\Contracts\NavigationDiscoveryRegistrarInterface;

final readonly class NavigationDiscoveryRegistrar implements NavigationDiscoveryRegistrarInterface
{
    public function __construct(
        private NavigationDiscoveryInterface $discovery,
        private NavigationManifestLoaderInterface $loader,
        private NavigationRegistrarInterface $registrar,
    ) {
    }

    /**
     * Descubre todos los manifiestos y registra
     * automáticamente grupos e ítems.
     */
    public function register(): void
    {
        foreach ($this->discovery->discover() as $path) {
            $manifest = $this->loader->load($path);

            foreach ($manifest->groups as $group) {
                $this->registrar->group($group);
            }

            foreach ($manifest->items as $item) {
                $this->registrar->item($item);
            }
        }
    }
}
