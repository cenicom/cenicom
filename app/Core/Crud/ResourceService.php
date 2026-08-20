<?php

declare(strict_types=1);

namespace App\Core\Crud;

use App\Core\Crud\Contracts\ResourceRegistryInterface;
use App\Core\Crud\Contracts\ResourceServiceInterface;

final readonly class ResourceService implements ResourceServiceInterface
{
    public function __construct(
        private ResourceRegistryInterface $resources,
    ) {
    }

    public function controller(string $resource): ?string
    {
        return $this->resources->controller($resource);
    }

    /**
     * @return array<string, string>
     */
    public function all(): array
    {
        return $this->resources->all();
    }
}
