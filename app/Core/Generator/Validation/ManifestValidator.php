<?php

declare(strict_types=1);

namespace App\Core\Generator\Validation;

use App\Core\Generator\Exceptions\InvalidManifestException;
use App\Core\Generator\Validation\Contracts\ValidatorInterface;


/**
 * ==========================================================
 * CENICOM ERP
 * ==========================================================
 *
 * Valida la estructura de un manifiesto del CN Generator.
 *
 * Su responsabilidad consiste en garantizar que el manifiesto
 * contenga la información mínima necesaria antes de ser
 * transformado en un objeto ModuleData.
 *
 * @package App\Core\Generator\Validation
 * @since 1.0.0
 */
final class ManifestValidator implements ValidatorInterface
{
    /**
     * Secciones obligatorias.
     *
     * @var array<int,string>
     */
    private const REQUIRED_SECTIONS = [
        'identity',
        'generation',
        'fields',
    ];

    /**
     * Campos obligatorios de identity.
     *
     * @var array<int,string>
     */
    private const REQUIRED_IDENTITY_FIELDS = [
        'name',
        'singular',
        'plural',
        'table',
        'description',
    ];

    /**
     * Valida el manifiesto completo.
     *
     * @param array<string,mixed> $manifest
     *
     * @throws InvalidManifestException
     */
    public function validate(array $manifest): void
    {
        $this->validateRequiredSections($manifest);

        $this->validateIdentity(
            $manifest['identity']
        );
    }

    /**
     * Valida las secciones obligatorias.
     *
     * @param array<string,mixed> $manifest
     *
     * @throws InvalidManifestException
     */
    private function validateRequiredSections(
        array $manifest
    ): void {

        foreach (self::REQUIRED_SECTIONS as $section) {

            if (! array_key_exists($section, $manifest)) {

                throw new InvalidManifestException(
                    sprintf(
                        'Missing required section [%s].',
                        $section
                    )
                );
            }
        }
    }

    /**
     * Valida la sección identity.
     *
     * @param array<string,mixed> $identity
     *
     * @throws InvalidManifestException
     */
    private function validateIdentity(
        array $identity
    ): void {

        foreach (self::REQUIRED_IDENTITY_FIELDS as $field) {

            $this->validateIdentityField(
                $identity,
                $field
            );
        }
    }

    /**
     * Valida un campo de identity.
     *
     * @param array<string,mixed> $identity
     *
     * @throws InvalidManifestException
     */
    private function validateIdentityField(
        array $identity,
        string $field
    ): void {

        if (! array_key_exists($field, $identity)) {

            throw new InvalidManifestException(
                sprintf(
                    'Identity field [%s] is required.',
                    $field
                )
            );
        }

        if (! is_string($identity[$field])) {

            throw new InvalidManifestException(
                sprintf(
                    'Identity field [%s] must be a string.',
                    $field
                )
            );
        }

        if (trim($identity[$field]) === '') {

            throw new InvalidManifestException(
                sprintf(
                    'Identity field [%s] cannot be empty.',
                    $field
                )
            );
        }
    }
}
