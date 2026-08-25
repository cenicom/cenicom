<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Crud;

use App\Core\Crud\Contracts\CrudActionAuthorizationInterface;
use App\Core\Crud\CrudAction;
use App\Core\Crud\CrudOperations;
use App\Core\Crud\DTO\CrudOperation;
use App\Core\Security\Contracts\IdentityInterface;
use Tests\TestCase;

final class CrudActionTest extends TestCase
{
    public function test_action_delegates_authorization_to_crud_action_authorization(): void
    {
        $identity = $this->createIdentity();

        $operation = new CrudOperation(
            CrudOperations::CREATE
        );

        $authorization = new class implements CrudActionAuthorizationInterface {
            public bool $called = false;

            public function allows(
                IdentityInterface $identity,
                string $resource,
                CrudOperation $operation,
            ): bool {
                $this->called = true;

                return $resource === 'institutions'
                    && $operation->name() === CrudOperations::CREATE;
            }
        };

        $action = new CrudAction(
            'institutions',
            $operation,
            $authorization,
        );

        self::assertTrue(
            $action->authorized($identity)
        );

        self::assertTrue(
            $authorization->called
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

    public function test_action_returns_false_when_authorization_denies(): void
    {
        $identity = $this->createIdentity();

        $operation = new CrudOperation(
            CrudOperations::DELETE
        );

        $authorization = new class implements CrudActionAuthorizationInterface {
            public function allows(
                IdentityInterface $identity,
                string $resource,
                CrudOperation $operation,
            ): bool {
                return false;
            }
        };

        $action = new CrudAction(
            'institutions',
            $operation,
            $authorization,
        );

        self::assertFalse(
            $action->authorized($identity)
        );
    }

    public function test_exposes_resource(): void
    {
        $action = $this->createAction();

        self::assertSame(
            'institutions',
            $action->resource(),
        );
    }

    public function test_exposes_operation(): void
    {
        $operation = new CrudOperation(CrudOperations::UPDATE);

        $action = new CrudAction(
            'institutions',
            $operation,
            $this->createAuthorization(),
        );

        self::assertSame(
            $operation,
            $action->operation(),
        );
    }

    private function createAction(): CrudAction
    {
        return new CrudAction(
            'institutions',
            new CrudOperation(CrudOperations::UPDATE),
            $this->createAuthorization(),
        );
    }

    private function createAuthorization(): CrudActionAuthorizationInterface
    {
        return new class implements CrudActionAuthorizationInterface
        {
            public function allows(
                IdentityInterface $identity,
                string $resource,
                CrudOperation $operation,
            ): bool {
                return true;
            }
        };
    }
}
