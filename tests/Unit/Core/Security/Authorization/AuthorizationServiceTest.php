<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Security\Authorization;

use App\Core\Security\Authorization\AuthorizationService;
use App\Core\Security\Authorization\Contracts\PermissionResolverInterface;
use App\Core\Security\Contracts\IdentityInterface;
use PHPUnit\Framework\TestCase;

final class AuthorizationServiceTest extends TestCase
{
    private AuthorizationService $service;

    private FakePermissionResolver $resolver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->resolver = new FakePermissionResolver();

        $this->service = new AuthorizationService(
            $this->resolver
        );
    }


    public function test_delegates_permission_check(): void
    {
        // Arrange

        $identity = new FakeIdentity();

        // Act

        $this->service->can(
            $identity,
            'users.view'
        );

        // Assert

        $this->assertTrue(
            $this->resolver->called
        );

        $this->assertSame(
            'users.view',
            $this->resolver->permission
        );
    }


    public function test_allows_granted_permission(): void
    {
        // Arrange

        $this->resolver->result = true;

        $identity = new FakeIdentity();


        // Act

        $result = $this->service->can(
            $identity,
            'users.view'
        );


        // Assert

        $this->assertTrue(
            $result
        );
    }


    public function test_denies_missing_permission(): void
    {
        // Arrange

        $this->resolver->result = false;

        $identity = new FakeIdentity();


        // Act

        $result = $this->service->can(
            $identity,
            'users.delete'
        );


        // Assert

        $this->assertFalse(
            $result
        );
    }
}


/**
 * Fake Permission Resolver para pruebas.
 */
final class FakePermissionResolver implements PermissionResolverInterface
{
    public bool $called = false;

    public bool $result = false;

    public ?string $permission = null;


    public function can(
        IdentityInterface $identity,
        string $permission
    ): bool {

        $this->called = true;

        $this->permission = $permission;

        return $this->result;
    }
}


/**
 * Fake Identity para pruebas.
 */
final class FakeIdentity implements IdentityInterface
{
    public function id(): int|string|null
    {
        return 1;
    }


    public function name(): string
    {
        return 'Test User';
    }


    public function roles(): array
    {
        return [];
    }


    public function permissions(): array
    {
        return [];
    }


    public function can(
        string $permission
    ): bool {
        return false;
    }


    public function authenticated(): bool
    {
        return true;
    }
}
