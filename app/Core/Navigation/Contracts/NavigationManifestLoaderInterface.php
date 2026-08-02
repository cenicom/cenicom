<?php

declare(strict_types=1);

namespace App\Core\Navigation\Contracts;

use App\Core\Navigation\DTO\NavigationManifestData;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Contrato para cargar Navigation Manifest.
 *
 * Responsabilidades:
 *
 * - Recibir la ruta del manifest.
 * - Transformar el archivo navigation.php
 *   en NavigationManifestData.
 *
 * No debe:
 *
 * - Descubrir archivos.
 * - Registrar navegación.
 * - Construir árboles.
 * - Resolver permisos.
 *
 * ==========================================================
 */
interface NavigationManifestLoaderInterface
{
    public function load(
        string $path,
    ): NavigationManifestData;
}
