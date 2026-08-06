<?php

declare(strict_types=1);

namespace App\Core\Module\Manifest;

use JsonException;

/**
 * Responsable de leer un ModuleManifest desde disco.
 *
 * Lee un archivo JSON, lo convierte en un ModuleManifest
 * utilizando ManifestFactory y posteriormente valida el
 * resultado mediante ManifestValidator.
 */
final readonly class ManifestReader
{
    public function __construct(
        private ManifestFactory $factory = new ManifestFactory(),
        private ManifestValidator $validator = new ManifestValidator(),
    ) {}

    /**
     * Lee un manifiesto desde un archivo JSON.
     *
     * @throws ManifestException
     */
    public function read(string $path): ModuleManifest
    {
        if (! is_file($path)) {
            throw new ManifestException(
                sprintf(
                    'Manifest [%s] does not exist.',
                    $path
                )
            );
        }

        if (! is_readable($path)) {
            throw new ManifestException(
                sprintf(
                    'Manifest [%s] is not readable.',
                    $path
                )
            );
        }

        $contents = file_get_contents($path);

        if ($contents === false) {
            throw new ManifestException(
                sprintf(
                    'Unable to read manifest [%s].',
                    $path
                )
            );
        }

        try {
            /** @var array<string,mixed> $definition */
            $definition = json_decode(
                $contents,
                true,
                512,
                JSON_THROW_ON_ERROR
            );
        } catch (JsonException $exception) {
            throw new ManifestException(
                'Invalid manifest JSON.',
                previous: $exception
            );
        }

        if (! is_array($definition)) {
            throw new ManifestException(
                'Manifest must decode to an array.'
            );
        }

        $manifest = $this->factory->create(
            $definition
        );

        $this->validator->validate(
            $manifest
        );

        return $manifest;
    }
}
