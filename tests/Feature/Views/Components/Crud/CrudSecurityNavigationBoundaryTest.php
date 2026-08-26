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

final class CrudSecurityNavigationBoundaryTest extends TestCase
{
    private function presentActions(
        IdentityInterface $identity,
        array $actions,
    ): array {
        $filter = new CrudActionFilter();

        $filtered = $filter->authorized(
            $identity,
            $actions,
        );

        $presenter = new CrudActionPresenter();

        $presentations = $presenter->present(
            $filtered,
        );

        $adapter = new CrudActionViewAdapter();

        return array_map(
            static fn ($presentation): CrudActionView =>
                $adapter->adapt($presentation),
            $presentations,
        );
    }

    public function test_authorized_crud_action_reaches_gui(): void
    {
        $identity = $this->createIdentity();

        $authorized = $this->createAction(true);

        $actions = $this->presentActions(
            $identity,
            [$authorized],
        );

        self::assertCount(1, $actions);

        self::assertInstanceOf(
            CrudActionView::class,
            $actions[0],
        );

        self::assertSame(
            $authorized,
            $actions[0]->action,
        );

        self::assertSame(
            'Editar',
            $actions[0]->label,
        );

        $view = $this->blade(
            '<x-cn.crud.actions :actions="$actions" />',
            [
                'actions' => $actions,
            ],
        );

        $view->assertSee('Editar');

        $view->assertSee(
            'cn-button--primary',
            false,
        );

        $view->assertSee(
            'cn-button--md',
            false,
        );
    }

    public function test_unauthorized_crud_action_never_reaches_gui(): void
    {
        $identity = $this->createIdentity();

        $unauthorized = $this->createAction(false);

        $actions = $this->presentActions(
            $identity,
            [$unauthorized],
        );

        self::assertSame([], $actions);

        $view = $this->blade(
            '<x-cn.crud.actions :actions="$actions" />',
            [
                'actions' => $actions,
            ],
        );

        $view->assertDontSee('Eliminar');
        $view->assertDontSee('Editar');
    }

    public function test_navigation_does_not_grant_crud_authorization(): void
    {
        $identity = $this->createIdentity();

        // La navegación puede exponer el recurso...
        $navigationResource = 'institutions';

        self::assertSame(
            'institutions',
            $navigationResource,
        );

        // ...pero no concede automáticamente la operación CRUD.
        $action = $this->createAction(false);

        $actions = $this->presentActions(
            $identity,
            [$action],
        );

        self::assertSame([], $actions);
    }

    public function test_gui_receives_only_filtered_crud_action_views(): void
    {
        $identity = $this->createIdentity();

        $authorized = $this->createAction(true);
        $unauthorized = $this->createAction(false);

        $actions = $this->presentActions(
            $identity,
            [
                $authorized,
                $unauthorized,
            ],
        );

        self::assertCount(1, $actions);

        self::assertInstanceOf(
            CrudActionView::class,
            $actions[0],
        );

        self::assertSame(
            $authorized,
            $actions[0]->action,
        );

        self::assertSame(
            'Editar',
            $actions[0]->label,
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
