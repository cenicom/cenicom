<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Crud;

use App\Core\Crud\Contracts\CrudRegistrarInterface;
use App\Core\Crud\CrudRegistrar;
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

    public function test_register_replaces_controller_for_existing_resource(): void
    {
        $registrar = new CrudRegistrar();

        $registrar->register(
            'users',
            'App\\Http\\Controllers\\UserController'
        );

        $registrar->register(
            'users',
            'App\\Http\\Controllers\\AdminUserController'
        );

        $this->assertSame(
            [
                'users' => 'App\\Http\\Controllers\\AdminUserController',
            ],
            $registrar->all()
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
}
