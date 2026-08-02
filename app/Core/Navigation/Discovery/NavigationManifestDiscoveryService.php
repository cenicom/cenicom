<?php

declare(strict_types=1);

namespace App\Core\Navigation\Discovery;

use App\Core\Navigation\Contracts\NavigationManifestDiscoveryInterface;
use App\Core\Navigation\Contracts\NavigationManifestFinderInterface;
use App\Core\Navigation\Contracts\NavigationManifestLoaderInterface;
use App\Core\Navigation\Contracts\NavigationManifestRegistrarInterface;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Orquesta el descubrimiento completo de Navigation Manifest.
 *
 * Responsabilidades:
 *
 * - Coordinar Finder.
 * - Coordinar Loader.
 * - Coordinar Registrar.
 *
 * No debe:
 *
 * - Buscar archivos.
 * - Leer archivos.
 * - Registrar directamente.
 * - Construir árboles.
 *
 * ==========================================================
 */
final readonly class NavigationManifestDiscoveryService
    implements NavigationManifestDiscoveryInterface
{
    public function __construct(
        private NavigationManifestFinderInterface $finder,
        private NavigationManifestLoaderInterface $loader,
        private NavigationManifestRegistrarInterface $registrar,
    ) {
    }

    public function discover(): void
    {
        $manifests = $this->finder->discover();

        foreach ($manifests as $manifest) {
            $loaded = $this->loader->load($manifest);

            $this->registrar->register($loaded);
        }
    }
}
