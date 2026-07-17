<?php

declare(strict_types=1);

namespace App\Core\Generator\Specifications;

use App\Core\Generator\Specifications\Contracts\SpecificationInterface;

final class ModuleSpecification implements SpecificationInterface
{
    /**
     * @param array<string,mixed> $definition
     */
    public function __construct(
        private readonly array $definition
    ) {
    }

    /**
     * @return array<string,mixed>
     */
    public function toArray(): array
    {
        return [
            'identity' => $this->identity(),
            'database' => $this->database(),
            'fields' => $this->fields(),
            'columns' => $this->columns(),
            'relations' => $this->relations(),
            'validation' => $this->validation(),
            'permissions' => $this->permissions(),
            'navigation' => $this->navigation(),
            'generation' => $this->generation(),
            'metadata' => $this->metadata(),
        ];
    }

    /**
     * @return array<string,mixed>
     */
    public function identity(): array
    {
        return $this->definition['identity'] ?? [];
    }
    /**
     * @return array<string,mixed>
     */
    public function database(): array
    {
        return $this->definition['database'] ?? [];
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    public function fields(): array
    {
        return $this->definition['fields'] ?? [];
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    public function columns(): array
    {
        return $this->definition['columns']
            ?? $this->definition['fields']
            ?? [];
    }

    /**
     * @return array<string,mixed>
     */

    public function relations(): array
    {
        return $this->definition['relations'] ?? [];
    }

    /**
     * @return array<string,mixed>
     */

    public function validation(): array
    {
        return $this->definition['validation'] ?? [];
    }

    /**
     * @return array<string,mixed>
     */

    public function permissions(): array
    {
        return $this->definition['permissions'] ?? [];
    }

    /**
     * @return array<string,mixed>
     */

    public function navigation(): array
    {
        return $this->definition['navigation'] ?? [];
    }

    /**
     * @return array<string,mixed>
     */

    public function generation(): array
    {
        return $this->definition['generation'] ?? [];
    }

    /**
     * @return array<string,mixed>
     */
    public function metadata(): array
    {
        return $this->definition['metadata'] ?? [];
    }


    public function version(): string
    {
        return $this->metadata()['version'] ?? '1.0';
    }
}
