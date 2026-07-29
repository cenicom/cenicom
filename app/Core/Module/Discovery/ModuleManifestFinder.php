<?php

declare(strict_types=1);

namespace App\Core\Module\Discovery;

use App\Core\Contracts\Module\ModuleManifestFinderInterface;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Implementación responsable de localizar los
 * manifiestos oficiales (module.php) de los módulos.
 *
 * ERP-INT-004
 *
 * @author CENICOM
 */
final class ModuleManifestFinder implements ModuleManifestFinderInterface
{
    public function __construct(
        private readonly string $modulesPath,
    ) {
        if (! is_dir($modulesPath)) {
            throw new \RuntimeException(
                sprintf(
                    'Modules directory not found: %s',
                    $modulesPath,
                ),
            );
        }
    }
    /**
     * Obtiene la colección de manifiestos
     * disponibles en el sistema.
     *
     * @return list<string>
     */
    public function find(): array
    {
        //
        $manifests = [];

        $directories = new \DirectoryIterator($this->modulesPath);

        foreach ($directories as $directory) {

            if (
                $directory->isDot()
                || ! $directory->isDir()
            ) {
                continue;
            }

            $manifest = $directory->getPathname()
                . DIRECTORY_SEPARATOR
                . 'module.php';

            if (is_file($manifest)) {
                $manifests[] = $manifest;
            }
        }

        return $manifests;
    }
}
