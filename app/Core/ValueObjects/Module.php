<?php

namespace App\Core\ValueObjects;

final readonly class Module
{
    public function __construct(

        public string $id,

        public string $title,

        public string $description,

        public string $icon,

        public string $color,

        public array $routes = [],

        public array $permissions = [],

        public array $navigation = [],

        public array $dashboard = [],

    ) {}
}
