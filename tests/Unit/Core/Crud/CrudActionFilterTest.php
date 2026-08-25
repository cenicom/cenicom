<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Crud;

use App\Core\Crud\Contracts\CrudActionAuthorizationInterface;
use App\Core\Crud\Contracts\CrudActionFilterInterface;
use App\Core\Crud\CrudAction;
use App\Core\Crud\CrudActionFilter;
use App\Core\Crud\CrudOperations;
use App\Core\Crud\DTO\CrudOperation;
use App\Core\Security\Contracts\IdentityInterface;
use Tests\TestCase;

final class CrudActionFilterTest extends TestCase
{
    public function test_implements_contract(): void
    {
        $filter = new CrudActionFilter();

        self::assertInstanceOf(
            CrudActionFilterInterface::class,
            $filter,
        );
    }

    public function test_keeps_authorized_actions(): void
    {
        $identity = $this->createIdentity();

        $action = $this->createAction(true);

        $filter = new CrudActionFilter();

        $result = $filter->authorized(
            $identity,
            [$action],
        );

        self::assertCount(1, $result);
        self::assertSame($action, $result[0]);
    }

    public function test_removes_unauthorized_actions(): void
    {
        $identity = $this->createIdentity();

        $action = $this->createAction(false);

        $filter = new CrudActionFilter();

        self::assertSame(
            [],
            $filter->authorized(
                $identity,
                [$action],
            ),
        );
    }

    public function test_preserves_only_authorized_actions_and_order(): void
    {
        $identity = $this->createIdentity();

        $view = $this->createAction(true);
        $edit = $this->createAction(false);
        $delete = $this->createAction(true);

        $filter = new CrudActionFilter();

        $result = $filter->authorized(
            $identity,
            [
                $view,
                $edit,
                $delete,
            ],
        );

        self::assertCount(2, $result);
        self::assertSame($view, $result[0]);
        self::assertSame($delete, $result[1]);
    }

    public function test_returns_empty_array_when_no_actions_are_provided(): void
    {
        $identity = $this->createIdentity();

        $filter = new CrudActionFilter();

        self::assertSame(
            [],
            $filter->authorized(
                $identity,
                [],
            ),
        );
    }

    private function createAction(bool $allowed): CrudAction
    {
        $authorization = new class ($allowed)
            implements CrudActionAuthorizationInterface
        {
            public function __construct(
                private bool $allowed,
            ) {
            }

            public function allows(
                IdentityInterface $identity,
                string $resource,
                CrudOperation $operation,
            ): bool {
                return $this->allowed;
            }
        };

        return new CrudAction(
            'institutions',
            new CrudOperation(CrudOperations::UPDATE),
            $authorization,
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
