<?php

declare(strict_types=1);

namespace App\Core\Generator\Specifications\Validators;

use App\Core\Generator\Specifications\Exceptions\InvalidSpecificationException;

final class SpecificationValidator
{
    /**
     * Valida una especificación completa.
     *
     * @param array<string,mixed> $definition
     *
     * @throws InvalidSpecificationException
     */
    public function validate(array $definition): void
    {
        $this->validateIdentity($definition);

        $this->validateFields($definition);

        $this->validateGeneration($definition);

        $this->validateMetadata($definition);
    }

    /**
     * Valida la identidad del módulo.
     *
     * @param array<string,mixed> $definition
     */
    private function validateIdentity(array $definition): void
    {
        if (! isset($definition['identity'])) {

            throw new InvalidSpecificationException(
                'Missing identity section.'
            );
        }

        $identity = $definition['identity'];

        if (! is_array($identity)) {

            throw new InvalidSpecificationException(
                'Identity section must be an array.'
            );
        }

        foreach ([
            'name',
            'singular',
            'plural',
            'table',
        ] as $field) {

            if (empty($identity[$field])) {

                throw new InvalidSpecificationException(
                    "Missing identity.{$field}"
                );
            }
        }
    }

    /**
     * Valida la definición de campos.
     *
     * @param array<string,mixed> $definition
     */
    private function validateFields(array $definition): void
    {
        if (! isset($definition['fields'])) {

            throw new InvalidSpecificationException(
                'Missing fields section.'
            );
        }

        if (! is_array($definition['fields'])) {

            throw new InvalidSpecificationException(
                'Fields must be an array.'
            );
        }

        foreach ($definition['fields'] as $index => $field) {

            if (! is_array($field)) {

                throw new InvalidSpecificationException(
                    "Field #{$index} is invalid."
                );
            }

            foreach ([
                'name',
                'type',
            ] as $required) {

                if (! isset($field[$required])) {

                    throw new InvalidSpecificationException(
                        "Field #{$index} missing {$required}."
                    );
                }
            }
        }
    }

    /**
     * Valida la configuración de generación.
     *
     * @param array<string,mixed> $definition
     */
    private function validateGeneration(array $definition): void
    {
        if (! isset($definition['generation'])) {
            return;
        }

        if (! is_array($definition['generation'])) {

            throw new InvalidSpecificationException(
                'Generation section must be an array.'
            );
        }
    }

    /**
     * Valida metadatos.
     *
     * @param array<string,mixed> $definition
     */
    private function validateMetadata(array $definition): void
    {
        if (! isset($definition['metadata'])) {
            return;
        }

        if (! is_array($definition['metadata'])) {

            throw new InvalidSpecificationException(
                'Metadata section must be an array.'
            );
        }
    }
}
