<?php

declare(strict_types=1);

namespace App\Core\Security\Permissions\DTO;

final readonly class PermissionDefinition
{
    public function __construct(
        public string $name,
        public string $description = '',
        public ?string $module = null,
    ) {
    }

    public function key(): string
    {
        return $this->name;
    }
}
