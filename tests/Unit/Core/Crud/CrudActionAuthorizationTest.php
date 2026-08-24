<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Crud;

use App\Core\Crud\Contracts\CrudPermissionResolverInterface;
use App\Core\Crud\CrudActionAuthorization;
use App\Core\Crud\CrudOperations;
use App\Core\Crud\DTO\CrudOperation;
use App\Core\Security\Authorization\Contracts\AuthorizationServiceInterface;
use App\Core\Security\Contracts\IdentityInterface;
use Tests\TestCase;

final class CrudActionAuthorizationTest extends TestCase
{
    public function test_allows_returns_true_when_authorization_service_allows_permission(): void
    {
        $identity = $this->createIdentity();

        $authorization = $this->createMock(
            AuthorizationServiceInterface::class
        );

        $permissionResolver = $this->createMock(
            CrudPermissionResolverInterface::class
        );

        $operation = new CrudOperation(
            CrudOperations::CREATE
        );

        $permissionResolver
            ->expects(self::once())
            ->method('permission')
            ->with(
                'institutions',
                $operation,
            )
            ->willReturn('institutions.create');

        $authorization
            ->expects(self::once())
            ->method('can')
            ->with(
                $identity,
                'institutions.create',
            )
            ->willReturn(true);

        $service = new CrudActionAuthorization(
            $authorization,
            $permissionResolver,
        );

        self::assertTrue(
            $service->allows(
                $identity,
                'institutions',
                $operation,
            )
        );
    }

    public function test_allows_returns_false_when_authorization_service_denies_permission(): void
    {
        $identity = $this->createIdentity();

        $authorization = $this->createMock(
            AuthorizationServiceInterface::class
        );

        $permissionResolver = $this->createMock(
            CrudPermissionResolverInterface::class
        );

        $operation = new CrudOperation(
            CrudOperations::DELETE
        );

        $permissionResolver
            ->expects(self::once())
            ->method('permission')
            ->with(
                'institutions',
                $operation,
            )
            ->willReturn('institutions.delete');

        $authorization
            ->expects(self::once())
            ->method('can')
            ->with(
                $identity,
                'institutions.delete',
            )
            ->willReturn(false);

        $service = new CrudActionAuthorization(
            $authorization,
            $permissionResolver,
        );

        self::assertFalse(
            $service->allows(
                $identity,
                'institutions',
                $operation,
            )
        );
    }

    public function test_resolves_permission_before_authorization(): void
    {
        $identity = $this->createIdentity();

        $authorization = $this->createMock(
            AuthorizationServiceInterface::class
        );

        $permissionResolver = $this->createMock(
            CrudPermissionResolverInterface::class
        );

        $operation = new CrudOperation(
            CrudOperations::VIEW
        );

        $permissionResolver
            ->expects(self::once())
            ->method('permission')
            ->with(
                'institutions',
                $operation,
            )
            ->willReturn('institutions.view');

        $authorization
            ->expects(self::once())
            ->method('can')
            ->with(
                $identity,
                'institutions.view',
            )
            ->willReturn(true);

        $service = new CrudActionAuthorization(
            $authorization,
            $permissionResolver,
        );

        self::assertTrue(
            $service->allows(
                $identity,
                'institutions',
                $operation,
            )
        );
    }

    private function createIdentity(): IdentityInterface
    {
        return new class implements IdentityInterface {
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

            public function can(string $permission): bool
            {
                return false;
            }

            public function authenticated(): bool
            {
                return true;
            }
        };
    }
}
