<?php

declare(strict_types=1);

namespace App\Core\Navigation\Discovery;

use App\Core\Navigation\Contracts\NavigationManifestLoaderInterface;
use App\Core\Navigation\DTO\NavigationManifestData;
use Illuminate\Support\Facades\File;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Carga un archivo navigation.php y genera
 * NavigationManifestData.
 *
 * Responsabilidades:
 *
 * - Leer manifest descubierto.
 * - Transformar configuración a DTO.
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
final readonly class NavigationManifestLoader
implements NavigationManifestLoaderInterface
{
    public function load(
        string $path,
    ): NavigationManifestData {

        if (! File::exists($path)) {
            throw new \RuntimeException(
                "Navigation manifest not found: {$path}"
            );
        }

        /** @var array{
         *     groups?: array<int, mixed>,
         *     items?: array<int, mixed>
         * } $navigation
         */
        $navigation = require $path;

        if (! is_array($navigation)) {
            throw new \UnexpectedValueException(
                sprintf(
                    'Navigation manifest must return an array: %s',
                    $path
                )
            );
        }

        if (
            isset($navigation['groups']) &&
            ! is_array($navigation['groups'])
        ) {
            throw new \UnexpectedValueException(
                'Navigation groups must be an array.'
            );
        }

        if (
            isset($navigation['items']) &&
            ! is_array($navigation['items'])
        ) {
            throw new \UnexpectedValueException(
                'Navigation items must be an array.'
            );
        }


        return new NavigationManifestData(
            module: basename(
                dirname($path)
            ),
            groups: $navigation['groups'] ?? [],
            items: $navigation['items'] ?? [],
        );
    }
}
