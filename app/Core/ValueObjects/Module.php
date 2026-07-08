<?php

declare(strict_types=1);

namespace App\Core\ValueObjects;

final readonly class Module
{
    public function __construct(
        public string $id,
        public string $name,
        public string $description = '',
        public string $version = '1.0.0',
        public bool $enabled = true,
        public string $icon = 'cube',
        public string $color = 'primary',
        public int $order = 0,

        /** @var array<class-string> */
        public array $providers = [],

        /** @var array<int,array<string,mixed>> */
        public array $navigation = [],

        /** @var array<int,string> */
        public array $permissions = [],
    ) {
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }
}