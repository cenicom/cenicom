<?php

namespace App\Core\Contracts;

interface KernelInterface
{
    public function register(
        string $name,
        object $service
    ): static;

    public function registry(
        string $name
    ): ?object;
}
