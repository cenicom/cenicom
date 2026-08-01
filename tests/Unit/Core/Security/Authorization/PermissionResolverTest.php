<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Security\Authorization;

use App\Core\Security\Authorization\PermissionResolver;
use App\Core\Security\Authorization\Contracts\RolePermissionResolverInterface;
use App\Core\Security\Contracts\IdentityInterface;
use Mockery;
use Tests\TestCase;

final class PermissionResolverTest extends TestCase
{
    /**
     * @var \Mockery\MockInterface&RolePermissionResolverInterface
     */
    private RolePermissionResolverInterface $rolePermissionResolver;

    private PermissionResolver $resolver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->rolePermissionResolver = Mockery::mock(
            RolePermissionResolverInterface::class
        );

        $this->resolver = new PermissionResolver(
            $this->rolePermissionResolver
        );
    }

    /**
     * Permite acceso cuando la identidad posee el permiso.
     */
    public function test_allows_permission_when_identity_has_permission(): void
    {
        $identity = Mockery::mock(IdentityInterface::class);

        $this->rolePermissionResolver
            ->shouldReceive('hasRolePermission')
            ->once()
            ->with(
                $identity,
                'users.create'
            )
            ->andReturn(true);

        $result = $this->resolver->can(
            $identity,
            'users.create'
        );

        $this->assertTrue($result);
    }

    /**
     * Niega acceso cuando la identidad no posee el permiso.
     */
    public function test_denies_permission_when_identity_has_no_permission(): void
    {
        $identity = Mockery::mock(IdentityInterface::class);

        $this->rolePermissionResolver
            ->shouldReceive('hasRolePermission')
            ->once()
            ->with(
                $identity,
                'users.delete'
            )
            ->andReturn(false);

        $result = $this->resolver->can(
            $identity,
            'users.delete'
        );

        $this->assertFalse($result);
    }

    /**
     * Niega cualquier permiso cuando la identidad invitada no tiene roles.
     */
    public function test_denies_permission_for_guest_identity(): void
    {
        $guestIdentity = Mockery::mock(IdentityInterface::class);

        $this->rolePermissionResolver
            ->shouldReceive('hasRolePermission')
            ->once()
            ->with(
                $guestIdentity,
                'users.create'
            )
            ->andReturn(false);

        $result = $this->resolver->can(
            $guestIdentity,
            'users.create'
        );

        $this->assertFalse($result);
    }

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }
}

