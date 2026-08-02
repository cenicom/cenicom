<?php

declare(strict_types=1);

namespace App\Core\Navigation\Bootstrap;

use App\Core\Navigation\Contracts\NavigationManifestBootstrapperInterface;
use App\Core\Navigation\Contracts\NavigationManifestDiscoveryInterface;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Inicializa automáticamente los manifests de navegación
 * durante el arranque del sistema.
 *
 * Responsabilidades:
 *
 * - Ejecutar discovery.
 * - Preparar navegación modular.
 *
 * No debe:
 *
 * - Construir árboles.
 * - Resolver permisos.
 * - Registrar directamente grupos/items.
 *
 * ==========================================================
 */
final readonly class NavigationManifestBootstrapper
    implements NavigationManifestBootstrapperInterface
{
    public function __construct(
        private NavigationManifestDiscoveryInterface $discovery,
    ) {
    }

    public function boot(): void
    {
        $this->discovery->discover();
    }
}
