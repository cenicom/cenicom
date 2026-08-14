<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Security\Authorization;

use App\Core\Security\Authorization\AuthorizationService;
use App\Core\Security\Authorization\Contracts\PermissionResolverInterface;
use App\Core\Security\Contracts\IdentityInterface;
use App\Core\Security\Policies\Contracts\PolicyInterface;
use App\Core\Security\Policies\Contracts\PolicyResolverInterface;
use PHPUnit\Framework\TestCase;

final class AuthorizationServiceTest extends TestCase
{
    private AuthorizationService $service;

    private FakePermissionResolver $resolver;

    private FakePolicyResolver $policyResolver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->resolver = new FakePermissionResolver();

        $this->policyResolver = new FakePolicyResolver();

        $this->service = new AuthorizationService(
            $this->resolver,
            $this->policyResolver,
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

    public function test_delegates_policy_resolution(): void
    {
        $identity = new FakeIdentity();

        $policy = new class implements PolicyInterface {
            public function allows(
                IdentityInterface $identity,
                mixed $resource
            ): bool {
                return true;
            }
        };

        $this->policyResolver->resolvedPolicy = $policy;

        $resource = new \stdClass();

        $this->service->allows(
            $identity,
            'institution',
            $resource,
        );

        self::assertTrue(
            $this->policyResolver->called
        );

        self::assertSame(
            'institution',
            $this->policyResolver->policy
        );
    }

    public function test_denies_when_policy_does_not_exist(): void
    {
        $identity = new FakeIdentity();

        $this->policyResolver->resolvedPolicy = null;

        self::assertFalse(
            $this->service->allows(
                $identity,
                'institution',
                new \stdClass(),
            )
        );
    }

    public function test_delegates_authorization_to_resolved_policy(): void
    {
        $identity = new FakeIdentity();

        $resource = new \stdClass();

        $policy = new class implements PolicyInterface {
            public bool $called = false;

            public function allows(
                IdentityInterface $identity,
                mixed $resource
            ): bool {
                $this->called = true;

                return true;
            }
        };

        $this->policyResolver->resolvedPolicy = $policy;

        $result = $this->service->allows(
            $identity,
            'institution',
            $resource,
        );

        self::assertTrue($result);
        self::assertTrue($policy->called);
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

/**
 * Fake Policy Resolver para pruebas.
 */
final class FakePolicyResolver implements PolicyResolverInterface
{
    public bool $called = false;

    public ?string $policy = null;

    public ?PolicyInterface $resolvedPolicy = null;

    public function resolve(
        string $policy
    ): ?PolicyInterface {
        $this->called = true;
        $this->policy = $policy;

        return $this->resolvedPolicy;
    }
}
