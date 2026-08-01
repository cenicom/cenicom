<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Security\Permissions;

use App\Core\Security\Permissions\Contracts\PermissionRegistrarInterface;
use App\Core\Security\Permissions\PermissionRegistrar;
use Tests\TestCase;

final class PermissionRegistrarContractTest extends TestCase
{
    public function test_registrar_implements_contract(): void
    {
        $registrar = $this->app->make(
            PermissionRegistrarInterface::class
        );

        $this->assertInstanceOf(
            PermissionRegistrarInterface::class,
            $registrar
        );
    }


    public function test_container_resolves_registrar_interface(): void
    {
        $registrar = $this->app->make(
            PermissionRegistrarInterface::class
        );

        $this->assertNotNull(
            $registrar
        );
    }


    public function test_resolved_instance_is_permission_registrar(): void
    {
        $registrar = $this->app->make(
            PermissionRegistrarInterface::class
        );

        $this->assertInstanceOf(
            PermissionRegistrar::class,
            $registrar
        );
    }


    public function test_registrar_is_singleton(): void
    {
        $first = $this->app->make(
            PermissionRegistrarInterface::class
        );

        $second = $this->app->make(
            PermissionRegistrarInterface::class
        );

        $this->assertSame(
            $first,
            $second
        );
    }
}
