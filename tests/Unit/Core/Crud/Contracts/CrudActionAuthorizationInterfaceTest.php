<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Crud\Contracts;

use App\Core\Crud\Contracts\CrudActionAuthorizationInterface;
use App\Core\Crud\CrudOperations;
use App\Core\Crud\DTO\CrudOperation;
use App\Core\Security\Contracts\IdentityInterface;
use Tests\TestCase;

final class CrudActionAuthorizationInterfaceTest extends TestCase
{
    public function test_contract_allows_authorization_for_crud_operation(): void
    {
        $identity = $this->createIdentity();

        $authorization = new class implements CrudActionAuthorizationInterface {
            public function allows(
                IdentityInterface $identity,
                string $resource,
                CrudOperation $operation,
            ): bool {
                return $resource === 'institutions'
                    && $operation->name() === CrudOperations::VIEW;
            }
        };

        self::assertTrue(
            $authorization->allows(
                $identity,
                'institutions',
                new CrudOperation(CrudOperations::VIEW),
            )
        );
    }

    public function test_contract_can_deny_crud_operation(): void
    {
        $identity = $this->createIdentity();

        $authorization = new class implements CrudActionAuthorizationInterface {
            public function allows(
                IdentityInterface $identity,
                string $resource,
                CrudOperation $operation,
            ): bool {
                return false;
            }
        };

        self::assertFalse(
            $authorization->allows(
                $identity,
                'institutions',
                new CrudOperation(CrudOperations::DELETE),
            )
        );
    }

    public function test_contract_receives_resource_and_operation_independently(): void
    {
        $identity = $this->createIdentity();

        $authorization = new class implements CrudActionAuthorizationInterface {
            public function allows(
                IdentityInterface $identity,
                string $resource,
                CrudOperation $operation,
            ): bool {
                return $resource === 'institutions'
                    && $operation->name() === CrudOperations::CREATE;
            }
        };

        self::assertTrue(
            $authorization->allows(
                $identity,
                'institutions',
                new CrudOperation(CrudOperations::CREATE),
            )
        );

        self::assertFalse(
            $authorization->allows(
                $identity,
                'institutions',
                new CrudOperation(CrudOperations::UPDATE),
            )
        );

        self::assertFalse(
            $authorization->allows(
                $identity,
                'users',
                new CrudOperation(CrudOperations::CREATE),
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
