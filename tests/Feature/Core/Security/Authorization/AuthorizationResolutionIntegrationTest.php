<?php

declare(strict_types=1);

namespace Tests\Feature\Core\Security\Authorization;

use App\Core\Security\Authorization\Contracts\AuthorizationServiceInterface;
use App\Core\Security\Contracts\IdentityInterface;
use App\Core\Security\Roles\Contracts\RoleRegistryInterface;
use App\Core\Security\Roles\DTO\RoleDefinition;
use Tests\TestCase;

final class AuthorizationResolutionIntegrationTest extends TestCase
{
    protected function tearDown(): void
    {
        app(RoleRegistryInterface::class)->clear();

        parent::tearDown();
    }

    public function test_allows_direct_permission_through_real_authorization_stack(): void
    {
        $service = app(
            AuthorizationServiceInterface::class
        );

        $identity = $this->identity(
            permissions: ['users.view']
        );

        $this->assertTrue(
            $service->can(
                $identity,
                'users.view'
            )
        );
    }

    public function test_allows_role_permission_through_real_authorization_stack(): void
    {
        $registry = app(
            RoleRegistryInterface::class
        );

        $registry->register(
            new RoleDefinition(
                name: 'administrator',
                label: 'Administrator',
                permissions: [
                    'institutions.view',
                ],
            )
        );

        $service = app(
            AuthorizationServiceInterface::class
        );

        $identity = $this->identity(
            roles: ['administrator']
        );

        $this->assertTrue(
            $service->can(
                $identity,
                'institutions.view'
            )
        );
    }

    public function test_denies_missing_permission_through_real_authorization_stack(): void
    {
        $service = app(
            AuthorizationServiceInterface::class
        );

        $identity = $this->identity();

        $this->assertFalse(
            $service->can(
                $identity,
                'users.delete'
            )
        );
    }

    public function test_denies_guest_identity_through_real_authorization_stack(): void
    {
        $service = app(
            AuthorizationServiceInterface::class
        );

        $identity = $this->identity(
            authenticated: false
        );

        $this->assertFalse(
            $service->can(
                $identity,
                'users.view'
            )
        );
    }

    private function identity(
        array $permissions = [],
        array $roles = [],
        bool $authenticated = true,
    ): IdentityInterface {
        return new class(
            $permissions,
            $roles,
            $authenticated,
        ) implements IdentityInterface {
            public function __construct(
                private readonly array $permissions,
                private readonly array $roles,
                private readonly bool $authenticated,
            ) {
            }

            public function id(): int|string|null
            {
                return $this->authenticated
                    ? 1
                    : null;
            }

            public function name(): string
            {
                return $this->authenticated
                    ? 'Integration User'
                    : '';
            }

            public function roles(): array
            {
                return $this->roles;
            }

            public function permissions(): array
            {
                return $this->permissions;
            }

            public function can(
                string $permission
            ): bool {
                return in_array(
                    $permission,
                    $this->permissions,
                    true
                );
            }

            public function authenticated(): bool
            {
                return $this->authenticated;
            }
        };
    }
}
