<?php

declare(strict_types=1);

namespace App\Core\Module\Discovery;

use App\Core\Module\Discovery\DiscoveryException;
use App\Core\Module\Discovery\ModuleFinder;
use App\Core\Module\Manifest\ManifestException;
use App\Core\Module\Manifest\ManifestReader;
use App\Core\Module\Manifest\ManifestValidator;
use App\Core\Module\Manifest\ModuleManifest;



/**
 * Orquesta el descubrimiento de módulos del sistema.
 *
 * Coordina la búsqueda de directorios de módulos,
 * la lectura de sus manifiestos y su validación.
 *
 * No registra módulos ni interactúa con el contenedor.
 */
final readonly class ModuleDiscovery
{
    public function __construct(
        private ModuleFinder $finder,
        private ManifestReader $reader,
        private ManifestValidator $validator,
    ) {}

    /**
     * Descubre todos los módulos válidos.
     *
     * @return array<int, ModuleManifest>
     *
     * @throws DiscoveryException
     */
    public function discover(string $directory): array
    {
        $modules = [];

        foreach ($this->finder->find($directory) as $modulePath) {

        $manifestPath = $modulePath . DIRECTORY_SEPARATOR . 'module.php';

        try {
            $modules[] = $this->reader->read($manifestPath);
        } catch (ManifestException $e) {
            throw new DiscoveryException(
                $e->getMessage(),
                previous: $e
            );
        }
    }

        return $modules;
    }
}
