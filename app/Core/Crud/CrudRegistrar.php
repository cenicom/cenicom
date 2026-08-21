<?php

declare(strict_types=1);

namespace App\Core\Crud;

use App\Core\Crud\Contracts\CrudRegistrarInterface;
use App\Core\Crud\Contracts\ResourceRegistryInterface;
use App\Core\Crud\DTO\CrudOperation;

final class CrudRegistrar implements
    CrudRegistrarInterface,
    ResourceRegistryInterface
{
    /**
     * @var array<string, array{
     *     controller: string,
     *     operations: array<int, CrudOperation>
     * }>
     */
    private array $resources = [];

    /**
     * @param array<int, CrudOperation> $operations
     */
    public function register(
        string $resource,
        string $controller,
        array $operations = [],
    ): void {
        $this->resources[$resource] = [
            'controller' => $controller,
            'operations' => $operations,
        ];
    }

    /**
     * @return array<string, string>
     */
    public function all(): array
    {
        return array_map(
            static fn(array $resource): string => $resource['controller'],
            $this->resources
        );
    }

    public function controller(
        string $resource
    ): ?string {
        return $this->resources[$resource]['controller'] ?? null;
    }

    /**
     * @return array<int, CrudOperation>
     */
    public function operations(
        string $resource
    ): array {
        return $this->resources[$resource]['operations'] ?? [];
    }

    public function hasOperation(
        string $resource,
        string $operation
    ): bool {
        foreach ($this->operations($resource) as $crudOperation) {
            if ($crudOperation->name() === $operation) {
                return true;
            }
        }

        return false;
    }

    public function clear(): void
    {
        $this->resources = [];
    }
}
