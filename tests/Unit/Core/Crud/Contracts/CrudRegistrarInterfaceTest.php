<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Crud\Contracts;

use App\Core\Crud\Contracts\CrudRegistrarInterface;
use App\Core\Crud\CrudOperations;
use App\Core\Crud\DTO\CrudOperation;
use PHPUnit\Framework\TestCase;

final class CrudRegistrarInterfaceTest extends TestCase
{
    public function test_register_accepts_crud_operations(): void
    {
        $registrar = new class implements CrudRegistrarInterface {
            public function register(
                string $resource,
                string $controller,
                array $operations = [],
            ): void {
                // ...
            }

            public function operations(
                string $resource
            ): array {
                return [];
            }

            public function hasOperation(
                string $resource,
                string $operation
            ): bool {
                return false;
            }
        };

        $operations = [
            new CrudOperation(CrudOperations::VIEW),
            new CrudOperation(CrudOperations::CREATE),
            new CrudOperation(CrudOperations::UPDATE),
            new CrudOperation(CrudOperations::DELETE),
        ];
        $registrar->register(
            'users',
            'App\\Http\\Controllers\\UserController',
            $operations,
        );

        self::assertTrue(true);
    }

    public function test_register_allows_empty_operations(): void
    {
        $registrar = new class implements CrudRegistrarInterface {
            public function register(
                string $resource,
                string $controller,
                array $operations = [],
            ): void {
                // Contract verification only.
            }

            public function operations(
                string $resource
            ): array {
                return [];
            }

            public function hasOperation(
                string $resource,
                string $operation
            ): bool {
                return false;
            }
        };

        $registrar->register(
            'users',
            'App\\Http\\Controllers\\UserController',
        );

        self::assertTrue(true);
    }
}
