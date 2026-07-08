<?php

namespace App\Core\Foundation;

final class Kernel
{
    protected array $registries = [];

    public function register(string $name, object $registry): static
    {
        $this->registries[$name] = $registry;

        return $this;
    }

    public function registry(string $name): ?object
    {
        return $this->registries[$name] ?? null;
    }

    public function registries(): array
    {
        return $this->registries;
    }
}
