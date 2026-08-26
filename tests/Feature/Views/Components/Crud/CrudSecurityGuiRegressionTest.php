<?php

declare(strict_types=1);

namespace Tests\Feature\Views\Components\Crud;

use App\Core\Crud\Contracts\CrudActionAuthorizationInterface;
use App\Core\Crud\CrudAction;
use App\Core\Crud\CrudActionFilter;
use App\Core\Crud\CrudActionPresenter;
use App\Core\Crud\CrudOperations;
use App\Core\Crud\DTO\CrudOperation;
use App\Core\Security\Contracts\IdentityInterface;
use App\View\Components\Cn\Crud\CrudActionView;
use App\View\Components\Cn\Crud\CrudActionViewAdapter;
use Tests\TestCase;

final class CrudSecurityGuiRegressionTest extends TestCase
{
    /**
     * Summary of test_authorized_action_reaches_gui test 1
     * @return void
     */
    public function test_authorized_action_reaches_gui(): void
    {
        $identity = $this->createIdentity();

        $action = $this->createAction(true);

        $filter = new CrudActionFilter();

        $filtered = $filter->authorized(
            $identity,
            [$action],
        );

        self::assertCount(1, $filtered);

        self::assertSame($action, $filtered[0]);

        $presenter = new CrudActionPresenter();

        $presentations = $presenter->present(
            [$filtered[0]],
        );

        $adapter = new CrudActionViewAdapter();

        $presented = array_map(
            static fn($presentation): CrudActionView =>
            $adapter->adapt($presentation),
            $presentations,
        );

        self::assertCount(1, $presentations);

        self::assertCount(1, $presented);

        self::assertInstanceOf(
            CrudActionView::class,
            $presented[0],
        );

        $view = $this->blade(
            '<x-cn.crud.actions :actions="$actions" />',
            [
                'actions' => $presented,
            ],
        );

        $view->assertSee('Editar');

    }

    /**
     * / test 2
     * @return void
     */
    public function test_unauthorized_action_is_removed_before_gui(): void
    {
        $identity = $this->createIdentity();

        $authorized = $this->createAction(true);

        $unauthorized = $this->createAction(false);

        $filter = new CrudActionFilter();

        $filtered = $filter->authorized(
            $identity,
            [
                $authorized,
                $unauthorized,
            ],
        );

        self::assertCount(1, $filtered);

        self::assertSame(
            $authorized,
            $filtered[0],
        );

        $presenter = new CrudActionPresenter();

        $presentations = $presenter->present(
            [$filtered[0]],
        );

        $adapter = new CrudActionViewAdapter();

        $presented = array_map(
            static fn($presentation): CrudActionView =>
            $adapter->adapt($presentation),
            $presentations,
        );

        self::assertCount(1, $presented);

        $view = $this->blade(
            '<x-cn.crud.actions :actions="$actions" />',
            [
                'actions' => $presented,
            ],
        );

        $view->assertSee('Editar');

    }

    /**
     * / test 3
     * @return void
     */
    public function test_gui_receives_only_crud_action_views(): void
    {
        $identity = $this->createIdentity();

        $action = $this->createAction(true);

        $filter = new CrudActionFilter();

        $filtered = $filter->authorized(
            $identity,
            [$action],
        );

        $presenter = new CrudActionPresenter();

        $presentations = $presenter->present(
            [$filtered[0]],
        );

        $adapter = new CrudActionViewAdapter();

        $presented = array_map(
            static fn($presentation): CrudActionView =>
            $adapter->adapt($presentation),
            $presentations,
        );

        self::assertCount(1, $presented);

        self::assertInstanceOf(
            CrudActionView::class,
            $presented[0],
        );

        $view = $this->blade(
            '<x-cn.crud.actions :actions="$actions" />',
            [
                'actions' => $presented,
            ],
        );

        $view->assertSee(
            'Editar',
        );
    }

    /**
     * / test 4
     * @return void
     */
    public function test_authorized_action_order_is_preserved(): void
    {
        $identity = $this->createIdentity();

        $first = $this->createAction(true);

        $second = $this->createAction(false);

        $third = $this->createAction(true);

        $filter = new CrudActionFilter();

        $filtered = $filter->authorized(
            $identity,
            [
                $first,
                $second,
                $third,
            ],
        );

        self::assertCount(2, $filtered);

        self::assertSame(
            $first,
            $filtered[0],
        );

        self::assertSame(
            $third,
            $filtered[1],
        );

        $presenter = new CrudActionPresenter();

        $presentations = $presenter->present(
            [
                $filtered[0],
                $filtered[1],
            ],
        );

        self::assertCount(2, $presentations);

        self::assertSame(
            $first,
            $presentations[0]->action(),
        );

        self::assertSame(
            $third,
            $presentations[1]->action(),
        );

        $adapter = new CrudActionViewAdapter();

        $presented = array_map(
            static fn($presentation): CrudActionView =>
            $adapter->adapt($presentation),
            $presentations,
        );

        self::assertCount(2, $presented);

        self::assertSame(
            $first,
            $presented[0]->action,
        );

        self::assertSame(
            $third,
            $presented[1]->action,
        );
    }

    public function test_navigation_cannot_add_crud_actions(): void
    {
        $identity = $this->createIdentity();

        $action = $this->createAction(true);

        $filter = new CrudActionFilter();

        $filtered = $filter->authorized(
            $identity,
            [$action],
        );

        self::assertCount(1, $filtered);

        $presenter = new CrudActionPresenter();

        $presentations = $presenter->present(
            [$filtered[0]],
        );

        $adapter = new CrudActionViewAdapter();

        $presented = array_map(
            static fn($presentation): CrudActionView =>
            $adapter->adapt($presentation),
            $presentations,
        );

        self::assertCount(1, $presented);

        $navigationPayload = [
            'label' => 'Instituciones',
            'route' => 'institutions.index',
        ];

        $view = $this->blade(
            '<x-cn.crud.actions :actions="$actions" />',
            [
                'actions' => $presented,
                'navigation' => $navigationPayload,
            ],
        );

        $view->assertSee(
            'Editar',
        );

        $view->assertDontSee(
            'institutions.index',
        );
    }

    private function createAction(bool $allowed): CrudAction
    {
        $authorization = new class($allowed)
        implements CrudActionAuthorizationInterface
        {
            public function __construct(
                private bool $allowed,
            ) {}

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
            new CrudOperation(
                CrudOperations::UPDATE,
            ),
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
