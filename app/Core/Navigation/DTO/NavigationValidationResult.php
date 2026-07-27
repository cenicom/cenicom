<?php

declare(strict_types=1);

namespace App\Core\Navigation\DTO;

final readonly class NavigationValidationResult
{
    public function __construct(
        private bool $valid,
        private array $errors = [],
        private array $warnings = []
    ) {
    }

    public function valid(): bool
    {
        return $this->valid;
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function warnings(): array
    {
        return $this->warnings;
    }
}
