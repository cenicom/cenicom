<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Crud\Contracts;

use App\Core\Crud\Contracts\CrudRegistrarInterface;
use Tests\TestCase;

final class CrudRegistrarInterfaceTest extends TestCase
{
    public function test_contract_can_be_implemented(): void
    {
        $registrar = new class implements CrudRegistrarInterface {
            public function register(
                string $resource,
                string $controller,
            ): void {
            }
        };

        $this->assertInstanceOf(
            CrudRegistrarInterface::class,
            $registrar
        );
    }

    public function test_register_accepts_resource_and_controller(): void
    {
        $registrar = new class implements CrudRegistrarInterface {
            public function register(
                string $resource,
                string $controller,
            ): void {
            }
        };

        $registrar->register(
            'App\\Modules\\Institution\\Resources\\InstitutionResource',
            'App\\Modules\\Institution\\Http\\Controllers\\InstitutionController',
        );

        $this->assertTrue(true);
    }

    public function test_register_returns_void(): void
    {
        $registrar = new class implements CrudRegistrarInterface {
            public function register(
                string $resource,
                string $controller,
            ): void {
            }
        };

        $result = $registrar->register(
            'Institution',
            'InstitutionController',
        );

        $this->assertNull($result);
    }
}
