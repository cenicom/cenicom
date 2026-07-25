<?php

declare(strict_types=1);

namespace App\Core\Generator\DTO;

final readonly class PermissionMatrix
{
    /**
     * @param array<int,PermissionDefinition> $permissions
     */
    public function __construct(
        private array $permissions
    ) {
    }


    /**
     * @param array<int,array<string,mixed>> $permissions
     */
    public static function fromArray(
        array $permissions
    ): self {

        return new self(
            array_map(
                static fn(array $permission): PermissionDefinition =>
                    PermissionDefinition::fromArray($permission),
                $permissions
            )
        );
    }


    /**
     * @return array<int,PermissionDefinition>
     */
    public function permissions(): array
    {
        return $this->permissions;
    }
}
