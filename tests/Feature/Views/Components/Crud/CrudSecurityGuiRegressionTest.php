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
//use App\Core\Crud\Contracts\CrudActionPresentationInterface;
//use App\View\Components\Cn\Crud\CrudActionViewAdapter;
use Tests\TestCase;

final class CrudSecurityGuiRegressionTest extends TestCase
{
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

        $actionView = new CrudActionView(
            action: $filtered[0],
            label: 'Editar institución',
            href: '/institutions/1/edit',
            variant: 'primary',
            size: 'md',
            icon: 'fas fa-edit',
        );

        $presenter = new CrudActionPresenter();

        $presented = $presenter->present(
            [$actionView],
        );

        self::assertCount(1, $presented);
        self::assertSame($actionView, $presented[0]);

        $view = $this->blade(
            '<x-cn.crud.actions :actions="$actions" />',
            [
                'actions' => $presented,
            ],
        );

        $view->assertSee('Editar institución');
        $view->assertSee(
            '/institutions/1/edit',
            false,
        );
        $view->assertSee(
            'fa-edit',
            false,
        );
    }

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

        $authorizedView = new CrudActionView(
            action: $filtered[0],
            label: 'Editar institución',
            href: '/institutions/1/edit',
            icon: 'fas fa-edit',
        );

        $presenter = new CrudActionPresenter();

        $presented = $presenter->present(
            [$authorizedView],
        );

        self::assertCount(1, $presented);

        $view = $this->blade(
            '<x-cn.crud.actions :actions="$actions" />',
            [
                'actions' => $presented,
            ],
        );

        $view->assertSee('Editar institución');

        $view->assertDontSee(
            'Eliminar institución',
        );

        $view->assertDontSee(
            '/institutions/1/delete',
            false,
        );

        $view->assertDontSee(
            'fa-trash',
            false,
        );
    }

    public function test_gui_receives_only_crud_action_views(): void
    {
        $identity = $this->createIdentity();

        $action = $this->createAction(true);

        $filter = new CrudActionFilter();

        $filtered = $filter->authorized(
            $identity,
            [$action],
        );

        $actionView = new CrudActionView(
            action: $filtered[0],
            label: 'Ver institución',
        );

        $presenter = new CrudActionPresenter();

        $presented = $presenter->present(
            [$actionView],
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
            'Ver institución',
        );
    }

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

        $firstView = new CrudActionView(
            action: $filtered[0],
            label: 'Ver',
        );

        $thirdView = new CrudActionView(
            action: $filtered[1],
            label: 'Editar',
        );

        $presenter = new CrudActionPresenter();

        $presented = $presenter->present(
            [
                $firstView,
                $thirdView,
            ],
        );

        self::assertCount(2, $presented);

        self::assertSame(
            $firstView,
            $presented[0],
        );

        self::assertSame(
            $thirdView,
            $presented[1],
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

        $actionView = new CrudActionView(
            action: $filtered[0],
            label: 'Ver institución',
            href: '/institutions/1',
        );

        $presenter = new CrudActionPresenter();

        $presented = $presenter->present(
            [$actionView],
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
            'Ver institución',
        );

        $view->assertDontSee(
            'institutions.index',
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
