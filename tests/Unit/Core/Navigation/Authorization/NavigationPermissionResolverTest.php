<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Navigation\Authorization;

use App\Core\Navigation\Authorization\NavigationPermissionResolver;
use App\Core\Security\Authorization\Contracts\PermissionResolverInterface;
use App\Core\Security\Contracts\IdentityInterface;
use Mockery;
use Tests\TestCase;

final class NavigationPermissionResolverTest extends TestCase
{
    public function test_allows_public_navigation_item(): void
    {
        $permissionResolver = Mockery::mock(
            PermissionResolverInterface::class
        );

        $identity = Mockery::mock(
            IdentityInterface::class
        );

        $resolver = new NavigationPermissionResolver(
            $permissionResolver
        );

        $this->assertTrue(
            $resolver->canView(
                $identity,
                null
            )
        );
    }

    public function test_delegates_permission_check_to_security_layer(): void
    {
        $identity = Mockery::mock(
            IdentityInterface::class
        );

        $permissionResolver = Mockery::mock(
            PermissionResolverInterface::class
        );

        $permissionResolver
            ->shouldReceive('can')
            ->once()
            ->with(
                $identity,
                'institutions.view'
            )
            ->andReturn(true);

        $resolver = new NavigationPermissionResolver(
            $permissionResolver
        );

        $this->assertTrue(
            $resolver->canView(
                $identity,
                'institutions.view'
            )
        );
    }

    public function test_denies_navigation_when_security_layer_denies_permission(): void
    {
        $identity = Mockery::mock(
            IdentityInterface::class
        );

        $permissionResolver = Mockery::mock(
            PermissionResolverInterface::class
        );

        $permissionResolver
            ->shouldReceive('can')
            ->once()
            ->with(
                $identity,
                'users.delete'
            )
            ->andReturn(false);

        $resolver = new NavigationPermissionResolver(
            $permissionResolver
        );

        $this->assertFalse(
            $resolver->canView(
                $identity,
                'users.delete'
            )
        );
    }

    public function test_does_not_query_security_for_public_navigation(): void
    {
        $permissionResolver = Mockery::mock(
            PermissionResolverInterface::class
        );

        $permissionResolver
            ->shouldReceive('can')
            ->never();

        $identity = Mockery::mock(
            IdentityInterface::class
        );

        $resolver = new NavigationPermissionResolver(
            $permissionResolver
        );

        $this->assertTrue(
            $resolver->canView(
                $identity,
                null
            )
        );
    }
}
