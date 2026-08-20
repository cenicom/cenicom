<?php

declare(strict_types=1);

namespace App\Core\Crud\Contracts;

interface ResourceRegistryInterface
{
    public function controller(string $resource): ?string;

    /**
     * @return array<string, string>
     */
    public function all(): array;
}
