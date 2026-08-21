<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Crud;

use App\Core\Crud\Contracts\CrudPermissionResolverInterface;
use App\Core\Crud\Contracts\CrudPermissionServiceInterface;
use App\Core\Crud\Contracts\CrudRegistrarInterface;
use App\Core\Crud\CrudPermissionService;
use App\Core\Crud\CrudOperations;
use App\Core\Crud\DTO\CrudOperation;
use Tests\TestCase;

final class CrudPermissionServiceTest extends TestCase
{
    public function test_implements_contract(): void
    {
        $registrar = new class implements CrudRegistrarInterface {
            public function register(
                string $resource,
                string $controller,
                array $operations = [],
            ): void {
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

        $resolver = new class implements CrudPermissionResolverInterface {
            public function permission(
                string $resource,
                CrudOperation $operation,
            ): string {
                return $resource.'.'.$operation->name();
            }
        };

        $service = new CrudPermissionService(
            $registrar,
            $resolver,
        );

        self::assertInstanceOf(
            CrudPermissionServiceInterface::class,
            $service,
        );
    }

    public function test_resolves_permissions_from_registered_operations(): void
    {
        $registrar = new class implements CrudRegistrarInterface {
            public function register(
                string $resource,
                string $controller,
                array $operations = [],
            ): void {
            }

            public function operations(
                string $resource
            ): array {
                return [
                    new CrudOperation(CrudOperations::VIEW),
                    new CrudOperation(CrudOperations::CREATE),
                ];
            }

            public function hasOperation(
                string $resource,
                string $operation
            ): bool {
                return false;
            }
        };

        $resolver = new class implements CrudPermissionResolverInterface {
            public function permission(
                string $resource,
                CrudOperation $operation,
            ): string {
                return $resource.'.'.$operation->name();
            }
        };

        $service = new CrudPermissionService(
            $registrar,
            $resolver,
        );

        self::assertSame(
            [
                'users.view',
                'users.create',
            ],
            $service->permissions('users'),
        );
    }

    public function test_resolves_multiple_operations(): void
    {
        $registrar = new class implements CrudRegistrarInterface {
            public function register(
                string $resource,
                string $controller,
                array $operations = [],
            ): void {
            }

            public function operations(
                string $resource
            ): array {
                return [
                    new CrudOperation(CrudOperations::VIEW),
                    new CrudOperation(CrudOperations::CREATE),
                    new CrudOperation(CrudOperations::UPDATE),
                    new CrudOperation(CrudOperations::DELETE),
                ];
            }

            public function hasOperation(
                string $resource,
                string $operation
            ): bool {
                return false;
            }
        };

        $resolver = new class implements CrudPermissionResolverInterface {
            public function permission(
                string $resource,
                CrudOperation $operation,
            ): string {
                return $resource.'.'.$operation->name();
            }
        };

        $service = new CrudPermissionService(
            $registrar,
            $resolver,
        );

        self::assertSame(
            [
                'users.view',
                'users.create',
                'users.update',
                'users.delete',
            ],
            $service->permissions('users'),
        );
    }

    public function test_returns_empty_array_when_resource_has_no_operations(): void
    {
        $registrar = new class implements CrudRegistrarInterface {
            public function register(
                string $resource,
                string $controller,
                array $operations = [],
            ): void {
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

        $resolver = new class implements CrudPermissionResolverInterface {
            public function permission(
                string $resource,
                CrudOperation $operation,
            ): string {
                return $resource.'.'.$operation->name();
            }
        };

        $service = new CrudPermissionService(
            $registrar,
            $resolver,
        );

        self::assertSame(
            [],
            $service->permissions('users'),
        );
    }
}
