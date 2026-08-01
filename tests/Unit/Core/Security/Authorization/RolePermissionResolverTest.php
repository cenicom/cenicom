<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Security\Authorization;

use App\Core\Security\Authorization\RolePermissionResolver;
use App\Core\Security\Contracts\IdentityInterface;
use App\Core\Security\Roles\Contracts\RoleRegistryInterface;
use App\Core\Security\Roles\DTO\RoleDefinition;
use Mockery;
use Tests\TestCase;

final class RolePermissionResolverTest extends TestCase
{
    public function test_allows_permission_when_identity_role_has_permission(): void
    {
        $registry = Mockery::mock(RoleRegistryInterface::class);

        $registry
            ->shouldReceive('role')
            ->with('admin')
            ->andReturn(
                new RoleDefinition(
                    'admin',
                    'Administrator',
                    [
                        'users.view',
                    ]
                )
            );

        $identity = Mockery::mock(IdentityInterface::class);

        $identity
            ->shouldReceive('authenticated')
            ->andReturnTrue();

        $identity
            ->shouldReceive('roles')
            ->andReturn(['admin']);

        $resolver = new RolePermissionResolver($registry);

        $this->assertTrue(
            $resolver->hasRolePermission(
                $identity,
                'users.view'
            )
        );
    }


    public function test_denies_permission_when_role_does_not_have_permission(): void
    {
        $registry = Mockery::mock(RoleRegistryInterface::class);

        $registry
            ->shouldReceive('role')
            ->with('admin')
            ->andReturn(
                new RoleDefinition(
                    'admin',
                    'Administrator',
                    []
                )
            );

        $identity = Mockery::mock(IdentityInterface::class);

        $identity
            ->shouldReceive('authenticated')
            ->andReturnTrue();

        $identity
            ->shouldReceive('roles')
            ->andReturn(['admin']);

        $resolver = new RolePermissionResolver($registry);

        $this->assertFalse(
            $resolver->hasRolePermission(
                $identity,
                'users.delete'
            )
        );
    }


    public function test_denies_everything_for_guest_identity(): void
    {
        $registry = Mockery::mock(RoleRegistryInterface::class);

        $identity = Mockery::mock(IdentityInterface::class);

        $identity
            ->shouldReceive('authenticated')
            ->andReturnFalse();

        $resolver = new RolePermissionResolver($registry);

        $this->assertFalse(
            $resolver->hasRolePermission(
                $identity,
                'anything'
            )
        );
    }
}
