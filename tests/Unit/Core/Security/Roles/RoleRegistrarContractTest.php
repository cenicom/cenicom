<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Security\Roles;

use App\Core\Security\Roles\Contracts\RoleRegistrarInterface;
use App\Core\Security\Roles\Contracts\RoleRegistryInterface;
use App\Core\Security\Roles\RoleRegistrar;
use Tests\TestCase;

final class RoleRegistrarContractTest extends TestCase
{
    public function test_registrar_implements_contract(): void
    {
        $registrar = new RoleRegistrar(
            $this->app->make(RoleRegistryInterface::class)
        );

        $this->assertInstanceOf(
            RoleRegistrarInterface::class,
            $registrar
        );
    }

    public function test_container_resolves_registrar_interface(): void
    {
        $registrar = $this->app->make(
            RoleRegistrarInterface::class
        );

        $this->assertNotNull(
            $registrar
        );
    }

    public function test_resolved_instance_is_role_registrar(): void
    {
        $registrar = $this->app->make(
            RoleRegistrarInterface::class
        );

        $this->assertInstanceOf(
            RoleRegistrar::class,
            $registrar
        );
    }

    public function test_registrar_is_singleton(): void
    {
        $first = $this->app->make(
            RoleRegistrarInterface::class
        );

        $second = $this->app->make(
            RoleRegistrarInterface::class
        );

        $this->assertSame(
            $first,
            $second
        );
    }
}
