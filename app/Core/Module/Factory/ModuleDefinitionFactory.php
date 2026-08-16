<?php

declare(strict_types=1);

namespace App\Core\Module\Factory;

use App\Core\Contracts\Module\ModuleDefinitionFactoryInterface;
use App\Core\Module\DTO\ModuleDefinition;

/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Fábrica responsable de construir instancias de
 * ModuleDefinition a partir del manifiesto oficial
 * de un módulo.
 *
 * ERP-INT-004
 *
 * @author CENICOM
 */
final class ModuleDefinitionFactory implements ModuleDefinitionFactoryInterface
{

    /**
     * Construye una definición de módulo a partir
     * de su manifiesto.
     */
    public function create(string $manifestPath): ModuleDefinition
    {
        if (! is_file($manifestPath)) {
            throw new \RuntimeException(
                "Manifest not found: {$manifestPath}"
            );
        }

        $manifest = require $manifestPath;

        if (! is_array($manifest)) {
            throw new \UnexpectedValueException(
                'Module manifest must return an array.',
            );
        }

        if (
            array_key_exists('enabled', $manifest)
            && ! is_bool($manifest['enabled'])
        ) {
            throw new \UnexpectedValueException(
                'Module manifest "enabled" must be a boolean.',
            );
        }

        if (! array_key_exists('name', $manifest)) {
            throw new \UnexpectedValueException(
                'Module manifest must define the "name" key.',
            );
        }

        if (! is_string($manifest['name']) || $manifest['name'] === '') {
            throw new \UnexpectedValueException(
                'Module manifest "name" must be a non-empty string.',
            );
        }

        if (! array_key_exists('namespace', $manifest)) {
            throw new \UnexpectedValueException(
                'Module manifest must define the "namespace" key.',
            );
        }

        if (! is_string($manifest['namespace']) || $manifest['namespace'] === '') {
            throw new \UnexpectedValueException(
                'Module manifest "namespace" must be a non-empty string.',
            );
        }

        if (! array_key_exists('providers', $manifest)) {
            throw new \UnexpectedValueException(
                'Module manifest must define the "providers" key.',
            );
        }

        if (! is_array($manifest['providers'])) {
            throw new \UnexpectedValueException(
                'Module manifest "providers" must be an array.',
            );
        }

        if (
            array_key_exists('permission_definitions', $manifest)
            && ! is_array($manifest['permission_definitions'])
        ) {
            throw new \UnexpectedValueException(
                'Module manifest "permission_definitions" must be an array.',
            );
        }

        $enabled = $manifest['enabled'] ?? true;

        $permissionDefinitions =
            $manifest['permission_definitions'] ?? [];

        return new ModuleDefinition(
            name: $manifest['name'],
            namespace: $manifest['namespace'],
            basePath: dirname($manifestPath),
            manifestPath: $manifestPath,
            providers: $manifest['providers'],
            permissionDefinitions: $permissionDefinitions,
            enabled: $enabled,
        );
    }
}
