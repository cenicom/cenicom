<?php

declare(strict_types=1);

namespace App\Core\Generator\Specifications\Loaders;

use App\Core\Generator\Specifications\Contracts\SpecificationInterface;
use App\Core\Generator\Specifications\Exceptions\InvalidSpecificationException;
use App\Core\Generator\Specifications\ModuleSpecification;
use App\Core\Generator\Specifications\Validators\SpecificationValidator;

final readonly class JsonSpecificationLoader
{
    public function __construct(
        private SpecificationValidator $validator,
    ) {
    }

    /**
     * Carga una especificación JSON.
     *
     * @throws InvalidSpecificationException
     */
    public function load(
        string $directory
    ): SpecificationInterface {

        $path = $this->resolvePath(
            $directory
        );

        $definition = $this->loadDefinition(
            $path
        );

        $this->validator->validate(
            $definition
        );

        return new ModuleSpecification(
            $definition
        );
    }

    /**
     * Obtiene la ruta del archivo specification.json.
     *
     * @throws InvalidSpecificationException
     */
    private function resolvePath(
        string $directory
    ): string {

        $path = rtrim(
            $directory,
            DIRECTORY_SEPARATOR
        ) . DIRECTORY_SEPARATOR . 'specification.json';

        if (! is_file($path)) {

            throw new InvalidSpecificationException(
                "Specification file not found: {$path}"
            );
        }

        return $path;
    }

    /**
     * Carga la definición JSON.
     *
     * @return array<string,mixed>
     *
     * @throws InvalidSpecificationException
     */
    private function loadDefinition(
        string $path
    ): array {

        $contents = file_get_contents(
            $path
        );

        if ($contents === false) {

            throw new InvalidSpecificationException(
                "Unable to read specification: {$path}"
            );
        }

        $definition = json_decode(
            $contents,
            true
        );

        if (json_last_error() !== JSON_ERROR_NONE) {

            throw new InvalidSpecificationException(
                'Invalid JSON specification: ' . json_last_error_msg()
            );
        }

        if (! is_array($definition)) {

            throw new InvalidSpecificationException(
                'Specification must decode to an array.'
            );
        }

        return $definition;
    }
}
