<?php

declare(strict_types=1);

namespace App\Core\Module\Manifest;

final class ManifestFactory
{
    /**
     * @param array<string,mixed> $definition
     */
    public function create(array $definition): ModuleManifest
    {
        $name = (string) ($definition['name'] ?? '');

        return new ModuleManifest(
            name: $name,

            slug: (string) (
                $definition['slug']
                ?? strtolower($name)
            ),

            description: (string) (
                $definition['description']
                ?? ''
            ),

            version: (string) (
                $definition['version']
                ?? '1.0.0'
            ),

            providers: array_values(
                $definition['providers'] ?? []
            ),

            dependencies: array_values(
                $definition['dependencies'] ?? []
            ),

            permissions: array_values(
                $definition['permissions'] ?? []
            ),

            navigation: array_values(
                $definition['navigation'] ?? []
            ),
        );
    }
}
