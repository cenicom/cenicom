<?php

namespace App\Core\Contracts;

interface RegistryInterface
{
    public function load(): static;

    public function all(): array;

    public function get(string $key): mixed;

    public function has(string $key): bool;

    public function clear(): static;
}
