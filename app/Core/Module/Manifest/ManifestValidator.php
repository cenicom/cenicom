<?php

declare(strict_types=1);

namespace App\Core\Module\Manifest;

use App\Core\Module\Manifest\ManifestException;

/**
 * Valida un ModuleManifest.
 *
 * Esta clase concentra todas las reglas de validación del
 * manifiesto antes de que el módulo sea registrado por
 * el Module Bootstrap.
 */
final class ManifestValidator
{
    /**
     * Valida un manifiesto.
     *
     * @throws ManifestException
     */
    public function validate(ModuleManifest $manifest): bool
    {
        $this->validateName($manifest);

        $this->validateSlug($manifest);

        $this->validateVersion($manifest);

        $this->validateProviders($manifest);

        $this->validateDependencies($manifest);

        $this->validatePermissions($manifest);

        $this->validateNavigation($manifest->navigation());

        return true;
    }

    /**
     * @throws ManifestException
     */
    private function validateName(ModuleManifest $manifest): void
    {
        if (trim($manifest->name()) === '') {
            throw new ManifestException(
                'Module name cannot be empty.'
            );
        }
    }

    /**
     * @throws ManifestException
     */
    private function validateSlug(ModuleManifest $manifest): void
    {
        $slug = trim($manifest->slug());

        if ($slug === '') {
            throw new ManifestException(
                'Module slug cannot be empty.'
            );
        }

        if (! preg_match('/^[a-z0-9\-]+$/', $slug)) {
            throw new ManifestException(
                'Invalid module slug.'
            );
        }
    }

    /**
     * @throws ManifestException
     */
    private function validateVersion(ModuleManifest $manifest): void
    {
        if (trim($manifest->version()) === '') {
            throw new ManifestException(
                'Module version cannot be empty.'
            );
        }
    }

    /**
     * @throws ManifestException
     */
    private function validateProviders(ModuleManifest $manifest): void
    {
        foreach ($manifest->providers() as $provider) {

            if (! is_string($provider) || trim($provider) === '') {
                throw new ManifestException(
                    'Invalid provider definition.'
                );
            }
        }
    }

    /**
     * @throws ManifestException
     */
    private function validateDependencies(ModuleManifest $manifest): void
    {
        foreach ($manifest->dependencies() as $dependency) {

            if (! is_string($dependency) || trim($dependency) === '') {
                throw new ManifestException(
                    'Invalid dependency definition.'
                );
            }
        }
    }

    /**
     * @throws ManifestException
     */
    private function validatePermissions(ModuleManifest $manifest): void
    {
        foreach ($manifest->permissions() as $permission) {

            if (! is_string($permission) || trim($permission) === '') {
                throw new ManifestException(
                    'Invalid permission definition.'
                );
            }
        }
    }

    /**
     * @throws ManifestException
     */
    private function validateNavigation(array $navigation): void
    {
        if ($navigation === []) {
            return;
        }

        // colección
        if (isset($navigation[0]) && is_array($navigation[0])) {
            foreach ($navigation as $item) {
                $this->validateNavigationItem($item);
            }

            return;
        }

        // un único elemento
        $this->validateNavigationItem($navigation);
    }

    private function validateNavigationItem(array $item): void
    {
        foreach (
            [
                'group',
                'label',
                'route',
                'icon',
            ] as $key
        ) {

            if (! array_key_exists($key, $item)) {
                throw new ManifestException(
                    "Missing navigation key '{$key}'."
                );
            }
        }
    }
}
