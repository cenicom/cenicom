<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Navigation;

use App\Core\Navigation\Authorization\NavigationPermissionResolver;
use App\Core\Navigation\Contracts\NavigationPermissionResolverInterface;
use App\Core\Security\Authorization\Contracts\AuthorizationServiceInterface;
use App\Core\Security\Providers\SecurityServiceProvider;
use App\Core\Navigation\NavigationServiceProvider;
use Tests\TestCase;

final class NavigationSecurityContainerResolutionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->app->register(
            SecurityServiceProvider::class
        );

        $this->app->register(
            NavigationServiceProvider::class
        );
    }

    public function test_navigation_permission_resolver_interface_resolves(): void
    {
        $resolver = $this->app->make(
            NavigationPermissionResolverInterface::class
        );

        self::assertInstanceOf(
            NavigationPermissionResolver::class,
            $resolver
        );
    }

    public function test_navigation_permission_resolver_resolves_authorization_service(): void
    {
        $resolver = $this->app->make(
            NavigationPermissionResolverInterface::class
        );

        self::assertInstanceOf(
            NavigationPermissionResolver::class,
            $resolver
        );

        $authorization = $this->app->make(
            AuthorizationServiceInterface::class
        );

        self::assertInstanceOf(
            AuthorizationServiceInterface::class,
            $authorization
        );
    }

    public function test_navigation_permission_resolver_is_singleton(): void
    {
        $first = $this->app->make(
            NavigationPermissionResolverInterface::class
        );

        $second = $this->app->make(
            NavigationPermissionResolverInterface::class
        );

        self::assertSame(
            $first,
            $second
        );
    }
}
