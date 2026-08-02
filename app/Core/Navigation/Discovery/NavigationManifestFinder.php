<?php

declare(strict_types=1);

namespace App\Core\Navigation\Discovery;

use App\Core\Navigation\Contracts\NavigationManifestFinderInterface;
use Illuminate\Support\Facades\File;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Descubre los archivos navigation.php de los módulos.
 *
 * Responsabilidades:
 *
 * - Buscar manifests de navegación.
 * - Devolver sus rutas físicas.
 *
 * No debe:
 *
 * - Leer manifests.
 * - Registrar navegación.
 * - Construir DTOs.
 * - Resolver permisos.
 *
 * ==========================================================
 */
final readonly class NavigationManifestFinder implements NavigationManifestFinderInterface
{
    /**
     * @return array<int, string>
     */
    public function discover(): array
    {
        $manifests = [];

        $modulesPath = base_path('modules');

        if (! File::isDirectory($modulesPath)) {
            return [];
        }

        foreach (File::directories($modulesPath) as $modulePath) {

            $manifest = $modulePath . DIRECTORY_SEPARATOR . 'navigation.php';

            if (File::exists($manifest)) {
                $manifests[] = $manifest;
            }
        }

        return $manifests;
    }
}
