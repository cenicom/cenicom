<?php

declare(strict_types=1);

namespace App\Core\Generator\Factories;

use App\Core\Generator\Specifications\Contracts\SpecificationInterface;

final class ModuleDefinitionFactory
{
    /**
     * Construye la definición esperada por ModuleDataFactory.
     *
     * @return array<string,mixed>
     */
    public function create(
        SpecificationInterface $specification
    ): array {

        return [

            ...$this->buildIdentity($specification),

            ...$this->buildFields($specification),

            ...$this->buildGeneration($specification),

            ...$this->buildMetadata($specification),
        ];
    }

    /**
     * @return array<string,mixed>
     */
    private function buildIdentity(
        SpecificationInterface $specification
    ): array
    {
        return $specification->identity();
    }

    /**
     * @return array<string,mixed>
     */
    private function buildFields(
        SpecificationInterface $specification
    ): array
    {
        return [
            'fields' => $specification->fields(),
        ];
    }

    /**
     * @return array<string,mixed>
     */
    private function buildGeneration(
        SpecificationInterface $specification
    ): array
    {
        return $specification->generation();
    }

    /**
     * @return array<string,mixed>
     */
    private function buildMetadata(
        SpecificationInterface $specification
    ): array
    {
        return $specification->metadata();
    }
}
