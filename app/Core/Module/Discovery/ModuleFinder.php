<?php

declare(strict_types=1);

namespace App\Core\Module\Discovery;

use DirectoryIterator;

/**
 * Localiza los directorios de módulos disponibles.
 *
 * Su única responsabilidad consiste en descubrir los
 * directorios candidatos que representan módulos del
 * sistema.
 *
 * No interpreta manifests.
 * No valida módulos.
 * No registra módulos.
 */
final readonly class ModuleFinder
{
    /**
     * Busca todos los módulos disponibles.
     *
     * @param string $directory
     *
     * @return array<int,string>
     *
     * @throws DiscoveryException
     */
    public function find(string $directory): array
    {
        if (! is_dir($directory)) {
            throw DiscoveryException::directoryNotFound(
                $directory
            );
        }

        if (! is_readable($directory)) {
            throw DiscoveryException::directoryNotReadable(
                $directory
            );
        }

        $modules = [];

        foreach (new DirectoryIterator($directory) as $item) {

            if ($item->isDot()) {
                continue;
            }

            if (! $item->isDir()) {
                continue;
            }

            $name = $item->getFilename();

            /*
            |--------------------------------------------------------------------------
            | Ignorar directorios ocultos
            |--------------------------------------------------------------------------
            */
            if (str_starts_with($name, '.')) {
                continue;
            }

            $modules[] = $item->getPathname();
        }

        sort($modules);

        if ($modules === []) {
            throw DiscoveryException::noModulesFound(
                $directory
            );
        }

        return $modules;
    }


}
