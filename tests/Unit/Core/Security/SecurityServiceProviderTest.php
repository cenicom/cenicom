<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Security;

use App\Core\Security\Providers\SecurityServiceProvider;
use App\Core\Security\Services\IdentityService;

use Tests\TestCase;

final class SecurityServiceProviderTest extends TestCase
{
    public function test_registers_identity_service(): void
    {
        $this->app->register(
            SecurityServiceProvider::class
        );

        $this->assertTrue(
            $this->app->bound(
                IdentityService::class
            )
        );
    }


    public function test_resolves_identity_service_from_container(): void
    {
        $this->app->register(
            SecurityServiceProvider::class
        );

        $service = $this->app->make(
            IdentityService::class
        );

        $this->assertInstanceOf(
            IdentityService::class,
            $service
        );
    }


    public function test_keeps_singleton_instance(): void
    {
        $this->app->register(
            SecurityServiceProvider::class
        );

        $first = $this->app->make(
            IdentityService::class
        );

        $second = $this->app->make(
            IdentityService::class
        );

        $this->assertSame(
            $first,
            $second
        );
    }

    public function test_boot_loads_and_bootstraps_permission_definitions(): void
    {
        $this->app->register(
            SecurityServiceProvider::class
        );

        $this->app->boot();

        $registry = $this->app->make(
            \App\Core\Security\Permissions\Contracts\PermissionRegistryInterface::class
        );

        $this->assertNotNull(
            $registry->permission('institutions.view')
        );

        $this->assertNotNull(
            $registry->permission('institutions.create')
        );

        $this->assertNotNull(
            $registry->permission('institutions.update')
        );

        $this->assertNotNull(
            $registry->permission('institutions.delete')
        );

        $this->assertNotNull(
            $registry->permission('inventory.products.view')
        );

        $this->assertNotNull(
            $registry->permission('inventory.categories.view')
        );

        $this->assertNotNull(
            $registry->permission('inventory.movements.view')
        );

        $this->assertCount(
            7,
            $registry->all()
        );
    }
}
