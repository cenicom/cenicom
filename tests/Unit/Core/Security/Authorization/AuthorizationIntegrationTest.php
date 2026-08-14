<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Security\Authorization;

use App\Core\Security\Authorization\AuthorizationService;
use App\Core\Security\Authorization\PermissionResolver;
use App\Core\Security\Authorization\RolePermissionResolver;
use App\Core\Security\Contracts\IdentityInterface;
use App\Core\Security\Policies\PolicyRegistry;
use App\Core\Security\Policies\PolicyResolver;
use App\Core\Security\Roles\DTO\RoleDefinition;
use App\Core\Security\Roles\RoleRegistry;
use PHPUnit\Framework\TestCase;

final class AuthorizationIntegrationTest extends TestCase
{
    public function test_role_permission_is_authorized_through_real_stack(): void
    {
        $roleRegistry = new RoleRegistry();

        $roleRegistry->register(
            new RoleDefinition(
                name: 'administrator',
                label: 'Administrador',
                permissions: [
                    'institutions.view',
                ],
            )
        );

        $rolePermissionResolver = new RolePermissionResolver(
            $roleRegistry
        );

        $permissionResolver = new PermissionResolver(
            $rolePermissionResolver
        );

        $policyRegistry = new PolicyRegistry();

        $policyResolver = new PolicyResolver(
            $policyRegistry
        );

        $authorization = new AuthorizationService(
            $permissionResolver,
            $policyResolver
        );

        $identity = new class implements IdentityInterface {
            public function id(): int|string|null
            {
                return 1;
            }

            public function name(): string
            {
                return 'Administrator';
            }

            public function roles(): array
            {
                return [
                    'administrator',
                ];
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
        };

        self::assertTrue(
            $authorization->can(
                $identity,
                'institutions.view'
            )
        );
    }


    public function test_role_without_permission_is_denied(): void
    {
        $roleRegistry = new RoleRegistry();

        $roleRegistry->register(
            new RoleDefinition(
                name: 'administrator',
                label: 'Administrador',
                permissions: [
                    'institutions.view',
                ],
            )
        );

        $rolePermissionResolver = new RolePermissionResolver(
            $roleRegistry
        );

        $permissionResolver = new PermissionResolver(
            $rolePermissionResolver
        );

        $policyRegistry = new PolicyRegistry();

        $policyResolver = new PolicyResolver(
            $policyRegistry
        );

        $authorization = new AuthorizationService(
            $permissionResolver,
            $policyResolver
        );

        $identity = new class implements IdentityInterface {
            public function id(): int|string|null
            {
                return 1;
            }

            public function name(): string
            {
                return 'Administrator';
            }

            public function roles(): array
            {
                return [
                    'administrator',
                ];
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
        };

        self::assertFalse(
            $authorization->can(
                $identity,
                'institutions.delete'
            )
        );
    }
}
