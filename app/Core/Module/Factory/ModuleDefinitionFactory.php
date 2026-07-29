<?php

declare(strict_types=1);

namespace App\Core\Module\Factory;

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
final class ModuleDefinitionFactory
{

    /**
     * Construye una definición de módulo a partir
     * de su manifiesto.
     */
    public function create(string $manifestPath): ModuleDefinition
    {
        $manifest = require $manifestPath;

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

        return new ModuleDefinition(
            name: $manifest['name'],
            namespace: $manifest['namespace'],
            basePath: dirname($manifestPath),
            manifestPath: $manifestPath,
            providers: $manifest['providers'],
        );

        //
    }
}
