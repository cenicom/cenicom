<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Security\Permissions;

use App\Core\Security\Permissions\Contracts\PermissionRegistryInterface;
use App\Core\Security\Permissions\PermissionRegistry;
use Tests\TestCase;

final class PermissionRegistryContractTest extends TestCase
{
    public function test_registry_implements_contract(): void
    {
        $registry = new PermissionRegistry();

        $this->assertInstanceOf(
            PermissionRegistryInterface::class,
            $registry
        );
    }


    public function test_container_resolves_permission_registry_interface(): void
    {
        $registry = $this->app->make(
            PermissionRegistryInterface::class
        );

        $this->assertInstanceOf(
            PermissionRegistryInterface::class,
            $registry
        );
    }


    public function test_resolved_instance_is_permission_registry(): void
    {
        $registry = $this->app->make(
            PermissionRegistryInterface::class
        );

        $this->assertInstanceOf(
            PermissionRegistry::class,
            $registry
        );
    }


    public function test_registry_is_singleton(): void
    {
        $first = $this->app->make(
            PermissionRegistryInterface::class
        );

        $second = $this->app->make(
            PermissionRegistryInterface::class
        );

        $this->assertSame(
            $first,
            $second
        );
    }
}
