<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Security\Authorization;

use App\Core\Security\Authorization\AuthorizationService;
use App\Core\Security\Authorization\Contracts\AuthorizationServiceInterface;
use Tests\TestCase;

final class AuthorizationServiceBindingTest extends TestCase
{
    public function test_service_implements_contract(): void
    {
        $service = $this->app->make(
            AuthorizationServiceInterface::class
        );

        $this->assertInstanceOf(
            AuthorizationServiceInterface::class,
            $service
        );
    }

    public function test_container_resolves_authorization_service_interface(): void
    {
        $service = $this->app->make(
            AuthorizationServiceInterface::class
        );

        $this->assertNotNull(
            $service
        );
    }

    public function test_resolved_instance_is_authorization_service(): void
    {
        $service = $this->app->make(
            AuthorizationServiceInterface::class
        );

        $this->assertInstanceOf(
            AuthorizationService::class,
            $service
        );
    }

    public function test_service_is_singleton(): void
    {
        $first = $this->app->make(
            AuthorizationServiceInterface::class
        );

        $second = $this->app->make(
            AuthorizationServiceInterface::class
        );

        $this->assertSame(
            $first,
            $second
        );
    }
}
