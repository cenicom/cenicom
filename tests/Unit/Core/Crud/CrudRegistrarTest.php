<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Crud;

use App\Core\Crud\Contracts\CrudRegistrarInterface;
use App\Core\Crud\CrudOperations;
use App\Core\Crud\CrudRegistrar;
use App\Core\Crud\DTO\CrudOperation;
use PHPUnit\Framework\TestCase;

final class CrudRegistrarTest extends TestCase
{
    public function test_registrar_implements_contract(): void
    {
        $registrar = new CrudRegistrar();

        $this->assertInstanceOf(
            CrudRegistrarInterface::class,
            $registrar
        );
    }

    public function test_register_stores_resource_and_controller(): void
    {
        $registrar = new CrudRegistrar();

        $registrar->register(
            'users',
            'App\\Http\\Controllers\\UserController'
        );

        $this->assertSame(
            [
                'users' => 'App\\Http\\Controllers\\UserController',
            ],
            $registrar->all()
        );
    }

    public function test_controller_returns_registered_controller(): void
    {
        $registrar = new CrudRegistrar();

        $registrar->register(
            'users',
            'App\\Http\\Controllers\\UserController'
        );

        $this->assertSame(
            'App\\Http\\Controllers\\UserController',
            $registrar->controller('users')
        );
    }

    public function test_controller_returns_null_for_unknown_resource(): void
    {
        $registrar = new CrudRegistrar();

        $this->assertNull(
            $registrar->controller('unknown')
        );
    }

    public function test_register_replaces_operations_for_existing_resource(): void
    {
        $registrar = new CrudRegistrar();

        $registrar->register(
            'users',
            'App\\Http\\Controllers\\UserController',
            [
                new CrudOperation(CrudOperations::VIEW),
                new CrudOperation(CrudOperations::CREATE),
                new CrudOperation(CrudOperations::CREATE),
            ],
        );

        $registrar->register(
            'users',
            'App\\Http\\Controllers\\AdminUserController',
            [
                new CrudOperation(CrudOperations::VIEW),
                new CrudOperation(CrudOperations::DELETE),
            ],
        );

        $this->assertSame(
            [
                CrudOperations::VIEW,
                CrudOperations::DELETE,
            ],
            array_map(
                static fn(CrudOperation $operation): string => $operation->name(),
                $registrar->operations('users')
            )
        );
    }

    public function test_clear_removes_registered_resources(): void
    {
        $registrar = new CrudRegistrar();

        $registrar->register(
            'users',
            'App\\Http\\Controllers\\UserController'
        );

        $registrar->clear();

        $this->assertSame(
            [],
            $registrar->all()
        );
    }

    public function test_register_stores_operations(): void
    {
        $registrar = new CrudRegistrar();

        $operations = [
            new CrudOperation(CrudOperations::VIEW),
            new CrudOperation(CrudOperations::CREATE),
            new CrudOperation(CrudOperations::UPDATE),
            new CrudOperation(CrudOperations::DELETE),
        ];

        $registrar->register(
            'users',
            'App\\Http\\Controllers\\UserController',
            $operations
        );

        self::assertSame(
            $operations,
            $registrar->operations('users')
        );
    }

    public function test_operations_returns_registered_operations(): void
    {
        $registrar = new CrudRegistrar();

        $operations = [
            new CrudOperation(CrudOperations::VIEW),
            new CrudOperation(CrudOperations::CREATE),
        ];

        $registrar->register(
            'users',
            'App\\Http\\Controllers\\UserController',
            $operations
        );

        self::assertSame(
            $operations,
            $registrar->operations('users')
        );
    }

    public function test_operations_returns_empty_array_for_unknown_resource(): void
    {
        $registrar = new CrudRegistrar();

        self::assertSame(
            [],
            $registrar->operations('unknown')
        );
    }

    public function test_has_operation_returns_true_when_registered(): void
    {
        $registrar = new CrudRegistrar();

        $registrar->register(
            'users',
            'App\\Http\\Controllers\\UserController',
            [
                new CrudOperation(CrudOperations::VIEW),
                new CrudOperation(CrudOperations::CREATE),
                new CrudOperation(CrudOperations::DELETE),
            ]
        );

        self::assertTrue(
            $registrar->hasOperation(
                'users',
                CrudOperations::DELETE
            )
        );
    }

    public function test_has_operation_returns_false_when_not_registered(): void
    {
        $registrar = new CrudRegistrar();

        $registrar->register(
            'users',
            'App\\Http\\Controllers\\UserController',
            [
                new CrudOperation(CrudOperations::VIEW),
                new CrudOperation(CrudOperations::CREATE),
            ]
        );

        self::assertFalse(
            $registrar->hasOperation(
                'users',
                CrudOperations::DELETE
            )
        );
    }

    public function test_has_operation_returns_false_for_unknown_resource(): void
    {
        $registrar = new CrudRegistrar();

        self::assertFalse(
            $registrar->hasOperation(
                'unknown',
                CrudOperations::VIEW
            )
        );
    }

    public function test_replaced_operations_are_no_longer_available(): void
    {
        $registrar = new CrudRegistrar();

        $registrar->register(
            'users',
            'App\\Http\\Controllers\\UserController',
            [
                new CrudOperation(CrudOperations::CREATE),
                new CrudOperation(CrudOperations::UPDATE),
            ],
        );

        $registrar->register(
            'users',
            'App\\Http\\Controllers\\AdminUserController',
            [
                new CrudOperation(CrudOperations::DELETE),
            ],
        );

        $this->assertFalse(
            $registrar->hasOperation(
                'users',
                CrudOperations::CREATE
            )
        );

        $this->assertFalse(
            $registrar->hasOperation(
                'users',
                CrudOperations::UPDATE
            )
        );

        $this->assertTrue(
            $registrar->hasOperation(
                'users',
                CrudOperations::DELETE
            )
        );
    }

    public function test_register_without_operations_clears_previous_operations(): void
    {
        $registrar = new CrudRegistrar();

        $registrar->register(
            'users',
            'App\\Http\\Controllers\\UserController',
            [
                new CrudOperation(CrudOperations::VIEW),
                new CrudOperation(CrudOperations::CREATE),
            ],
        );

        $registrar->register(
            'users',
            'App\\Http\\Controllers\\AdminUserController',
        );

        $this->assertSame(
            [],
            $registrar->operations('users')
        );
    }
}
