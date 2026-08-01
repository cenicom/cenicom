<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Security\Roles;

use App\Core\Security\Roles\Contracts\RoleRegistryInterface;
use App\Core\Security\Roles\RoleRegistry;
use Tests\TestCase;

final class RoleRegistryContractTest extends TestCase
{
    public function test_registry_implements_contract(): void
    {
        $registry = new RoleRegistry();

        $this->assertInstanceOf(
            RoleRegistryInterface::class,
            $registry
        );
    }

    public function test_container_resolves_role_registry_interface(): void
    {
        $registry = $this->app->make(
            RoleRegistryInterface::class
        );

        $this->assertNotNull(
            $registry
        );
    }

    public function test_resolved_instance_is_role_registry(): void
    {
        $registry = $this->app->make(
            RoleRegistryInterface::class
        );

        $this->assertInstanceOf(
            RoleRegistry::class,
            $registry
        );
    }

    public function test_registry_is_singleton(): void
    {
        $first = $this->app->make(
            RoleRegistryInterface::class
        );

        $second = $this->app->make(
            RoleRegistryInterface::class
        );

        $this->assertSame(
            $first,
            $second
        );
    }
}
