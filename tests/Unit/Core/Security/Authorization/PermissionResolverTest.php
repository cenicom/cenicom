<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Security\Authorization;

use App\Core\Security\Authorization\Contracts\RolePermissionResolverInterface;
use App\Core\Security\Authorization\PermissionResolver;
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
     * Permite acceso cuando la identidad posee
     * directamente el permiso solicitado.
     */
    public function test_allows_permission_when_identity_has_direct_permission(): void
    {
        $identity = Mockery::mock(IdentityInterface::class);

        $identity
            ->shouldReceive('permissions')
            ->once()
            ->andReturn([
                'users.create',
            ]);

        $this->rolePermissionResolver
            ->shouldNotReceive('hasRolePermission');

        $result = $this->resolver->can(
            $identity,
            'users.create'
        );

        $this->assertTrue($result);
    }

    /**
     * Permite acceso cuando el permiso no es directo
     * pero pertenece a uno de los roles de la identidad.
     */
    public function test_allows_permission_when_role_has_permission(): void
    {
        $identity = Mockery::mock(IdentityInterface::class);

        $identity
            ->shouldReceive('permissions')
            ->once()
            ->andReturn([
                'users.view',
            ]);

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
     * Niega acceso cuando la identidad no posee
     * el permiso directamente ni mediante un rol.
     */
    public function test_denies_permission_when_identity_has_no_permission(): void
    {
        $identity = Mockery::mock(IdentityInterface::class);

        $identity
            ->shouldReceive('permissions')
            ->once()
            ->andReturn([
                'users.view',
            ]);

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
     * Un permiso directo tiene prioridad y evita
     * consultar el resolver de roles.
     */
    public function test_direct_permission_has_priority_over_role_resolution(): void
    {
        $identity = Mockery::mock(IdentityInterface::class);

        $identity
            ->shouldReceive('permissions')
            ->once()
            ->andReturn([
                'users.update',
            ]);

        $this->rolePermissionResolver
            ->shouldNotReceive('hasRolePermission');

        $result = $this->resolver->can(
            $identity,
            'users.update'
        );

        $this->assertTrue($result);
    }

    /**
     * Niega cualquier permiso para una identidad invitada
     * sin permisos directos ni permisos provenientes de roles.
     */
    public function test_denies_permission_for_guest_identity(): void
    {
        $guestIdentity = Mockery::mock(IdentityInterface::class);

        $guestIdentity
            ->shouldReceive('permissions')
            ->once()
            ->andReturn([]);

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
