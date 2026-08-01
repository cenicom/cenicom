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
}
