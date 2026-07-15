<?php

declare(strict_types=1);

namespace App\Core\Generator\Specifications\Loaders;

use App\Core\Generator\Specifications\Contracts\SpecificationInterface;
use App\Core\Generator\Specifications\Exceptions\InvalidSpecificationException;
use App\Core\Generator\Specifications\Specification;
use App\Core\Generator\Specifications\Validators\SpecificationValidator;

final readonly class PhpSpecificationLoader
{
    public function __construct(
        private SpecificationValidator $validator,
    ) {
    }

    /**
     * Carga una especificación PHP.
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

        return new Specification(
            $definition
        );
    }

    /**
     * Obtiene la ruta del archivo specification.php.
     *
     * @throws InvalidSpecificationException
     */
    private function resolvePath(
        string $directory
    ): string {

        $path = rtrim(
            $directory,
            DIRECTORY_SEPARATOR
        ) . DIRECTORY_SEPARATOR . 'specification.php';

        if (! is_file($path)) {

            throw new InvalidSpecificationException(
                "Specification file not found: {$path}"
            );
        }

        return $path;
    }

    /**
     * Carga la definición PHP.
     *
     * @return array<string,mixed>
     *
     * @throws InvalidSpecificationException
     */
    private function loadDefinition(
        string $path
    ): array {

        $definition = require $path;

        if (! is_array($definition)) {

            throw new InvalidSpecificationException(
                'Specification must return an array.'
            );
        }

        return $definition;
    }
}
