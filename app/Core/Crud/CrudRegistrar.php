<?php

declare(strict_types=1);

namespace App\Core\Crud;

use App\Core\Crud\Contracts\CrudRegistrarInterface;
use App\Core\Crud\Contracts\ResourceRegistryInterface;

final class CrudRegistrar implements
    CrudRegistrarInterface,
    ResourceRegistryInterface
{
    /**
     * @var array<string, string>
     */
    private array $resources = [];

    public function register(
        string $resource,
        string $controller
    ): void {
        $this->resources[$resource] = $controller;
    }

    /**
     * @return array<string, string>
     */
    public function all(): array
    {
        return $this->resources;
    }

    public function controller(
        string $resource
    ): ?string {
        return $this->resources[$resource] ?? null;
    }

    public function clear(): void
    {
        $this->resources = [];
    }
}
