<?php

declare(strict_types=1);

namespace App\Core\Navigation\Discovery;

use App\Core\Navigation\Contracts\NavigationManifestRegistrarInterface;
use App\Core\Navigation\Contracts\NavigationRegistrarInterface;
use App\Core\Navigation\DTO\NavigationManifestData;


/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Registra los Navigation Manifest descubiertos
 * dentro del NavigationRegistry.
 *
 * Responsabilidades:
 *
 * - Registrar grupos.
 * - Registrar ítems.
 * - Delegar el registro al NavigationRegistrar.
 *
 * No debe:
 *
 * - Descubrir manifests.
 * - Leer archivos.
 * - Construir árboles.
 * - Resolver permisos.
 * - Renderizar vistas.
 *
 * ==========================================================
 */
final readonly class NavigationManifestRegistrar
    implements NavigationManifestRegistrarInterface
{
    public function __construct(
        private NavigationRegistrarInterface $registrar,
    ) {
    }

    public function register(
        NavigationManifestData $manifest
    ): void {
        foreach ($manifest->groups as $group) {
            $this->registrar->group($group);
        }

        foreach ($manifest->items as $item) {
            $this->registrar->item($item);
        }
    }
}
