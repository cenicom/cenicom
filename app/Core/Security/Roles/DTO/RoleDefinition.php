<?php

declare(strict_types=1);

namespace App\Core\Security\Roles\DTO;

final readonly class RoleDefinition
{
    /**
     * @param array<int, string> $permissions
     */
    public function __construct(
        public string $name,
        public string $label,
        public array $permissions = [],
    ) {
    }

    /**
     * Convierte el rol a arreglo.
     *
     * @return array{
     *     name: string,
     *     label: string,
     *     permissions: array<int, string>
     * }
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'label' => $this->label,
            'permissions' => $this->permissions,
        ];
    }
}
