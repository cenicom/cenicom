<?php

declare(strict_types=1);

namespace App\Core\Navigation\Discovery;

use App\Core\Navigation\Contracts\NavigationManifestLoaderInterface;
use App\Core\Navigation\DTO\NavigationGroupData;
use App\Core\Navigation\DTO\NavigationItemData;
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
    public function load(string $path): NavigationManifestData
    {
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

        $groups = array_map(
            static fn(array $group): NavigationGroupData =>
                new NavigationGroupData(
                    id: $group['id'],
                    label: $group['label'],
                    icon: $group['icon'] ?? null,
                    order: $group['order'] ?? 0,
                ),
            $navigation['groups'] ?? [],
        );

        $items = array_map(
            static fn(array $item): NavigationItemData =>
                new NavigationItemData(
                    id: $item['id'],
                    label: $item['label'],
                    route: $item['route'],
                    permission: $item['permission'] ?? null,
                    icon: $item['icon'] ?? null,
                    order: $item['order'] ?? 0,
                    group: $item['group'] ?? '',
                ),
            $navigation['items'] ?? [],
        );

        return new NavigationManifestData(
            module: basename(
                dirname($path)
            ),
            groups: $groups,
            items: $items,
        );
    }
}
