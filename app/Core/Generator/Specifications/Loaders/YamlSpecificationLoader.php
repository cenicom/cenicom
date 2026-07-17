<?php

declare(strict_types=1);

namespace App\Core\Generator\Specifications\Loaders;

use App\Core\Generator\Specifications\Contracts\SpecificationInterface;
use App\Core\Generator\Specifications\Exceptions\InvalidSpecificationException;
use App\Core\Generator\Specifications\ModuleSpecification;
use App\Core\Generator\Specifications\Validators\SpecificationValidator;
use Symfony\Component\Yaml\Yaml;

final readonly class YamlSpecificationLoader
{
    public function __construct(
        private SpecificationValidator $validator,
    ) {
    }

    /**
     * Carga una especificación YAML.
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
     * Obtiene la ruta del archivo specification.yml.
     *
     * @throws InvalidSpecificationException
     */
    private function resolvePath(
        string $directory
    ): string {

        $path = rtrim(
            $directory,
            DIRECTORY_SEPARATOR
        ) . DIRECTORY_SEPARATOR . 'specification.yml';

        if (! is_file($path)) {

            throw new InvalidSpecificationException(
                "Specification file not found: {$path}"
            );
        }

        return $path;
    }

    /**
     * Carga la definición YAML.
     *
     * @return array<string,mixed>
     *
     * @throws InvalidSpecificationException
     */
    private function loadDefinition(
        string $path
    ): array {

        if (! function_exists('yaml_parse_file')) {

            throw new InvalidSpecificationException(
                'YAML extension is not installed.'
            );
        }

        $definition = Yaml::parseFile($path);

        if (! is_array($definition)) {

            throw new InvalidSpecificationException(
                'Specification must decode to an array.'
            );
        }

        return $definition;
    }
}
